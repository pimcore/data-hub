<?php

declare(strict_types=1);

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\DataHubBundle\Tests\Service\Proposal;

use Codeception\Test\Unit;
use Pimcore\Bundle\DataHubBundle\Proposal\ConfigDocument;
use Pimcore\Bundle\DataHubBundle\Proposal\ConfigProposalPolicyInterface;

class ConfigDocumentTest extends Unit
{
    public function testReadableDropsBookkeepingAndWithheldPaths(): void
    {
        $readable = ConfigDocument::readable([
            'general' => ['name' => 'feed', 'modificationDate' => 1, 'writeable' => true],
            'permissions' => ['user' => []],
            'delivery' => ['filetype' => 'csv', 'transmitter_sftp' => ['password' => 'secret']],
        ], $this->policy());

        $this->assertSame(['general' => ['name' => 'feed'], 'delivery' => ['filetype' => 'csv']], $readable);
    }

    public function testOnlyWholeSectionsAreLeftToChangeControl(): void
    {
        $this->assertSame(['permissions'], ConfigDocument::withheldSections($this->policy()));
    }

    public function testAWithheldSectionIsRefusedRatherThanUnknown(): void
    {
        $proposed = ['permissions' => [], 'typo' => []];

        $this->assertSame(['typo'], ConfigDocument::unknownSections($proposed, $this->policy()));
        $this->assertSame(['permissions'], ConfigDocument::refusedPaths($proposed, $this->policy()));
    }

    public function testAWithheldKeyInsideAProposableSectionIsRefused(): void
    {
        $refused = ConfigDocument::refusedPaths(
            ['delivery' => ['filetype' => 'xml', 'transmitter_sftp' => ['host' => 'x']]],
            $this->policy(),
        );

        $this->assertSame(['delivery.transmitter_sftp'], $refused);
    }

    public function testFoldReplacesListsAndMergesMaps(): void
    {
        $state = ConfigDocument::fold(
            ['general' => ['name' => 'feed', 'group' => 'a'], 'workspaces' => ['object' => [['cpath' => '/a']]]],
            ['general' => ['group' => 'b'], 'workspaces' => ['object' => []]],
        );

        $this->assertSame(['name' => 'feed', 'group' => 'b'], $state['general']);
        $this->assertSame(['object' => []], $state['workspaces']);
    }

    public function testWithStoredRestoresWhatTheChangeSetNeverCarried(): void
    {
        $configuration = ConfigDocument::withStored(
            ['delivery' => ['filetype' => 'xml', 'transmitter_sftp' => ['host' => 'forged']]],
            ['delivery' => ['filetype' => 'csv', 'transmitter_sftp' => ['host' => 'real']], 'permissions' => ['user' => []]],
            $this->policy(),
        );

        $this->assertSame([
            'delivery' => ['filetype' => 'xml', 'transmitter_sftp' => ['host' => 'real']],
            'permissions' => ['user' => []],
        ], $configuration);
    }

    public function testWithStoredDropsAWithheldPathStorageNeverHad(): void
    {
        $configuration = ConfigDocument::withStored(
            ['delivery' => ['transmitter_sftp' => ['host' => 'forged']]],
            [],
            $this->policy(),
        );

        $this->assertSame(['delivery' => []], $configuration);
    }

    public function testNameValidation(): void
    {
        $this->assertTrue(ConfigDocument::isValidName('feed-2_b'));
        $this->assertFalse(ConfigDocument::isValidName('-feed'));
        $this->assertFalse(ConfigDocument::isValidName('bad name'));
        $this->assertFalse(ConfigDocument::isValidName('über'));
    }

    private function policy(): ConfigProposalPolicyInterface
    {
        $policy = $this->createStub(ConfigProposalPolicyInterface::class);
        $policy->method('proposable')->willReturn(['general', 'delivery']);
        $policy->method('withheld')->willReturn(['permissions', 'delivery.transmitter_sftp']);

        return $policy;
    }
}
