<?php declare(strict_types=1);

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\DataHubBundle\Tests\GraphQL\DataObjectInputProcessor;

use Codeception\Test\Unit;

/**
 * @internal
 */
class ClassificationstoreIdentifierTest extends Unit
{
    use ClassificationstoreFixtureTrait {
        deleteFixtures as protected _after;
    }

    public function testIdentifiersWriteTheSameValuesAsNumericIds(): void
    {
        $group = $this->createGroup('ident_group');
        $key = $this->createKey($group, 'ident_key');
        $byId = $this->createObject();
        $byIdentifier = $this->createObject();

        $this->mutate($byId, $this->input($group, $key, [['value' => 'v']]));
        $result = $this->mutate($byIdentifier, [
            'groups' => [[
                'group' => 'ident_group',
                'keys' => [['key' => 'ident_key', 'values' => [['value' => 'v']]]],
            ]],
        ]);

        $this->assertTrue($result['success'], $result['message']);
        $this->assertSame($this->store($byId)->getItems(), $this->store($byIdentifier)->getItems());
    }

    /**
     * @return array<string, array{0: callable, 1: string}>
     */
    public static function unresolvable(): array
    {
        return [
            'unknown group identifier' => [
                static fn () => ['groups' => [['group' => 'no_such_group', 'keys' => []]]],
                'no_such_group',
            ],
            'unknown key identifier' => [
                static fn ($g) => ['groups' => [[
                    'groupId' => $g->getId(),
                    'keys' => [['key' => 'no_such_key', 'values' => []]],
                ]]],
                'no_such_key',
            ],
            'unknown numeric pair' => [
                static fn () => ['groups' => [[
                    'groupId' => 999999998,
                    'keys' => [['keyId' => 999999997, 'values' => []]],
                ]]],
                '999999998',
            ],
            'key of another group' => [
                static fn ($g, $other) => ['groups' => [[
                    'groupId' => $g->getId(),
                    'keys' => [['keyId' => $other->getKeyId(), 'values' => []]],
                ]]],
                'is not a key of the group',
            ],
        ];
    }

    /**
     * @dataProvider unresolvable
     */
    public function testWhatDoesNotResolveIsRefusedAndNothingIsSaved(callable $input, string $token): void
    {
        $group = $this->createGroup('main_group');
        $otherGroup = $this->createGroup('side_group');
        $otherKey = $this->createKey($otherGroup, 'side_key');
        $object = $this->createObject();

        $result = $this->mutate($object, $input($group, $otherKey));

        $this->assertFalse($result['success']);
        $this->assertStringContainsString($token, $result['message']);
        $this->assertSame([], $this->store($object)->getItems());
    }
}
