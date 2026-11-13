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
use InvalidArgumentException;
use Pimcore\Bundle\DataHubBundle\Proposal\ConfigDocument;
use Pimcore\Bundle\DataHubBundle\Proposal\ConfigProposalPolicyInterface;
use function sprintf;

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
            [
                'delivery' => ['filetype' => 'csv', 'transmitter_sftp' => ['host' => 'real']],
                'permissions' => ['user' => []],
            ],
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

    public function testWithStoredRestoresAWithheldKeyUnderASectionTheStateLacks(): void
    {
        $configuration = ConfigDocument::withStored(
            ['delivery' => 'flat'],
            ['delivery' => ['transmitter_sftp' => ['host' => 'real']]],
            $this->policy(),
        );

        $this->assertSame(['delivery' => ['transmitter_sftp' => ['host' => 'real']]], $configuration);
    }

    public function testNameValidation(): void
    {
        $this->assertTrue(ConfigDocument::isValidName('feed-2_b'));
        $this->assertFalse(ConfigDocument::isValidName('-feed'));
        $this->assertFalse(ConfigDocument::isValidName('bad name'));
        $this->assertFalse(ConfigDocument::isValidName('über'));
    }

    public function testNormalizedIgnoresKeyOrderButKeepsListOrderAndTypes(): void
    {
        $this->assertSame(
            ConfigDocument::normalized(['b' => 1, 'a' => ['y' => 2, 'x' => 1]]),
            ConfigDocument::normalized(['a' => ['x' => 1, 'y' => 2], 'b' => 1]),
        );
        $this->assertNotSame(ConfigDocument::normalized(['l' => [1, 2]]), ConfigDocument::normalized(['l' => [2, 1]]));
        $this->assertNotSame(ConfigDocument::normalized(['v' => '01']), ConfigDocument::normalized(['v' => '1']));
    }

    public function testAWildcardPathHidesEveryMatchingKeyOnRead(): void
    {
        $readable = ConfigDocument::readable([
            'general' => ['name' => 'feed'],
            'deliveryDestination' => [
                'transmitter' => 'sftp',
                'transmitter_sftp' => ['password' => 'secret'],
                'transmitter_retired' => ['token' => 'secret'],
                'transmitterHint' => 'kept',
            ],
        ], $this->wildcardPolicy());

        $this->assertSame([
            'general' => ['name' => 'feed'],
            'deliveryDestination' => ['transmitter' => 'sftp', 'transmitterHint' => 'kept'],
        ], $readable);
    }

    public function testAProposalTouchingAWildcardMatchIsRefused(): void
    {
        $proposed = [
            'deliveryDestination' => [
                'transmitter' => 'ftp',
                'transmitter_ftp' => ['host' => 'x'],
                'transmitter_retired' => [],
            ],
        ];

        $this->assertSame(
            ['deliveryDestination.transmitter_ftp', 'deliveryDestination.transmitter_retired'],
            ConfigDocument::refusedPaths($proposed, $this->wildcardPolicy()),
        );
        $this->assertSame([], ConfigDocument::refusedPaths(
            ['deliveryDestination' => ['transmitter' => 'ftp']],
            $this->wildcardPolicy(),
        ));
        $this->assertSame([], ConfigDocument::withheldSections($this->wildcardPolicy()));
    }

    public function testAProposalOmittingTheWildcardBlockKeepsTheStoredSettings(): void
    {
        $stored = [
            'general' => ['name' => 'feed', 'description' => 'old'],
            'deliveryDestination' => [
                'transmitter' => 'sftp',
                'transmitter_sftp' => ['host' => 'real', 'password' => 'secret'],
                'transmitter_retired' => ['token' => 'secret'],
            ],
        ];
        // what an agent sends back after reading: the readable document, edited
        $proposed = ConfigDocument::readable($stored, $this->wildcardPolicy());
        $proposed['general']['description'] = 'new';
        $proposed['deliveryDestination']['transmitter'] = 'ftp';

        $this->assertSame([], ConfigDocument::refusedPaths($proposed, $this->wildcardPolicy()));

        $configuration = ConfigDocument::withStored(
            ConfigDocument::fold($stored, $proposed),
            $stored,
            $this->wildcardPolicy(),
        );
        $this->assertSame($stored['deliveryDestination']['transmitter_sftp'], $configuration['deliveryDestination']['transmitter_sftp']);
        $this->assertSame($stored['deliveryDestination']['transmitter_retired'], $configuration['deliveryDestination']['transmitter_retired']);
        $this->assertSame('ftp', $configuration['deliveryDestination']['transmitter']);
        $this->assertSame('new', $configuration['general']['description']);

        // a section sent whole, without the blocks, cannot drop them either
        $configuration = ConfigDocument::withStored(
            ['general' => $stored['general'], 'deliveryDestination' => ['transmitter' => 'ftp']],
            $stored,
            $this->wildcardPolicy(),
        );
        $this->assertSame([
            'transmitter' => 'ftp',
            'transmitter_sftp' => ['host' => 'real', 'password' => 'secret'],
            'transmitter_retired' => ['token' => 'secret'],
        ], $configuration['deliveryDestination']);
    }

    public function testAWildcardMatchCannotBeOverwrittenOrAdded(): void
    {
        $configuration = ConfigDocument::withStored(
            ['deliveryDestination' => [
                'transmitter_sftp' => ['host' => 'forged'],
                'transmitter_new' => ['host' => 'forged'],
                'transmitterHint' => 'changed',
            ]],
            ['deliveryDestination' => ['transmitter_sftp' => ['host' => 'real'], 'transmitterHint' => 'old']],
            $this->wildcardPolicy(),
        );

        $this->assertSame(
            ['deliveryDestination' => ['transmitterHint' => 'changed', 'transmitter_sftp' => ['host' => 'real']]],
            $configuration,
        );
    }

    public function testAnAddressUnderAWildcardMatchIsWithheld(): void
    {
        $policy = $this->wildcardPolicy();

        $this->assertTrue(ConfigDocument::withholds(['deliveryDestination', 'transmitter_sftp', 'host'], 'x', $policy));
        $this->assertTrue(ConfigDocument::withholds(['deliveryDestination', 'transmitter_x'], [], $policy));
        $this->assertTrue(ConfigDocument::withholds(['deliveryDestination'], ['transmitter_x' => []], $policy));
        $this->assertFalse(ConfigDocument::withholds(['deliveryDestination'], ['transmitter' => 'ftp'], $policy));
        $this->assertFalse(ConfigDocument::withholds(['deliveryDestination', 'transmitter'], 'ftp', $policy));
        $this->assertFalse(ConfigDocument::withholds(['general', 'transmitter_sftp'], 'x', $policy));
    }

    public function testExactPathsStillMatchOnlyThemselves(): void
    {
        $policy = $this->policy();
        $document = ['delivery' => ['transmitter_sftp' => ['host' => 'a'], 'transmitter_ftp' => ['host' => 'b']]];

        $this->assertSame(['delivery' => ['transmitter_ftp' => ['host' => 'b']]], ConfigDocument::readable($document, $policy));
        $this->assertSame(['delivery.transmitter_sftp'], ConfigDocument::refusedPaths($document, $policy));
        $this->assertTrue(ConfigDocument::withholds(['delivery', 'transmitter_sftp'], 'x', $policy));
        $this->assertFalse(ConfigDocument::withholds(['delivery', 'transmitter_ftp'], 'x', $policy));
        $this->assertTrue(ConfigDocument::withholds(['permissions', 'user'], [], $policy));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function unsupportedWildcards(): iterable
    {
        yield 'wildcard section' => ['transmitter_*'];
        yield 'wildcard in a parent segment' => ['delivery*.transmitter'];
        yield 'wildcard inside the last segment' => ['delivery.trans*mitter'];
        yield 'two wildcards' => ['delivery.transmitter_**'];
    }

    /**
     * @dataProvider unsupportedWildcards
     */
    public function testAWildcardAnywhereButAtTheEndOfTheLastSegmentIsRefused(string $path): void
    {
        $policy = $this->createStub(ConfigProposalPolicyInterface::class);
        $policy->method('withheld')->willReturn([$path]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Withheld path "%s" is not supported', $path));
        ConfigDocument::readable(['delivery' => ['transmitter' => 'x']], $policy);
    }

    private function wildcardPolicy(): ConfigProposalPolicyInterface
    {
        $policy = $this->createStub(ConfigProposalPolicyInterface::class);
        $policy->method('proposable')->willReturn(['general', 'deliveryDestination']);
        $policy->method('withheld')->willReturn(['deliveryDestination.transmitter_*']);

        return $policy;
    }

    private function policy(): ConfigProposalPolicyInterface
    {
        $policy = $this->createStub(ConfigProposalPolicyInterface::class);
        $policy->method('proposable')->willReturn(['general', 'delivery']);
        $policy->method('withheld')->willReturn(['permissions', 'delivery.transmitter_sftp']);

        return $policy;
    }
}
