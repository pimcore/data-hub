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
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreType\Feature;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreType\Group;
use Pimcore\Model\DataObject\Classificationstore\KeyConfig;

/**
 * @internal
 */
class ClassificationstoreTest extends Unit
{
    use ClassificationstoreFixtureTrait {
        deleteFixtures as protected _after;
    }

    private const IDENTIFIER_GROUP = 'datahub_write_group';

    private const IMPERIAL = 'imperial';

    public static function writableTypes(): array
    {
        return [
            'input' => ['input', 'first', 'second'],
            'textarea' => ['textarea', 'first', 'second'],
            'numeric' => ['numeric', 5, 6],
            'checkbox' => ['checkbox', true, false],
        ];
    }

    /**
     * @dataProvider writableTypes
     */
    public function testAValueIsWrittenAndOverwritten(string $type, mixed $first, mixed $second): void
    {
        $group = $this->createGroup(self::IDENTIFIER_GROUP);
        $key = $this->createKey($group, 'k_' . $type, $type);
        $object = $this->createObject();

        foreach ([$first, $second] as $value) {
            $result = $this->mutate($object, $this->input($group, $key, [['value' => $value]]));

            $this->assertTrue($result['success'], $result['message']);
            $stored = $this->store($object)->getLocalizedKeyValue($group->getId(), $key->getKeyId(), 'default');
            $this->assertSame($value, $stored);
        }
    }

    public function testTheWriteIsAMergeThatLeavesEveryOtherSlotAlone(): void
    {
        $groupA = $this->createGroup('group_a');
        $groupB = $this->createGroup('group_b');
        $keyA1 = $this->createKey($groupA, 'a1');
        $keyA2 = $this->createKey($groupA, 'a2');
        $keyB = $this->createKey($groupB, 'b1');
        $object = $this->createObject();

        $this->mutate($object, ['groups' => [
            ['groupId' => $groupA->getId(), 'keys' => [
                ['keyId' => $keyA1->getKeyId(), 'values' => [['value' => 'a1']]],
                ['keyId' => $keyA2->getKeyId(), 'values' => [['value' => 'a2']]],
            ]],
            ['groupId' => $groupA->getId(), 'language' => 'de', 'keys' => [
                ['keyId' => $keyA1->getKeyId(), 'values' => [['value' => 'a1-de']]],
            ]],
            ['groupId' => $groupB->getId(), 'keys' => [
                ['keyId' => $keyB->getKeyId(), 'values' => [['value' => 'b1']]],
            ]],
        ]]);
        $before = $this->store($object)->getItems();

        $result = $this->mutate($object, $this->input($groupA, $keyA1, [['value' => 'changed']]));
        $this->assertTrue($result['success'], $result['message']);

        $expected = $before;
        $expected[$groupA->getId()][$keyA1->getKeyId()]['default'] = 'changed';
        $this->assertSame($expected, $this->store($object)->getItems());
    }

    public function testExplicitNullClearsOnlyTheNamedSlot(): void
    {
        $group = $this->createGroup(self::IDENTIFIER_GROUP);
        $key = $this->createMultiValueKey($group, 'weight');
        $object = $this->createObject();
        $entries = [
            ['value' => 'v0'],
            ['value' => 'v1', 'position' => 1],
            ['value' => 'i0', 'variant' => self::IMPERIAL],
        ];
        $this->mutate($object, $this->input($group, $key, $entries));

        $result = $this->mutate($object, $this->input($group, $key, [['value' => null]]));

        $this->assertTrue($result['success'], $result['message']);
        $store = $this->store($object);
        $this->assertNull($store->getLocalizedKeyValue($group->getId(), $key->getKeyId(), 'default'));
        $this->assertSame([1 => 'v1'], $store->getKeyValues($group->getId(), $key->getKeyId()));
        $this->assertSame([0 => 'i0'], $store->getKeyValues($group->getId(), $key->getKeyId(), null, self::IMPERIAL));
    }

    public function testAnOmittedKeyIsLeftAlone(): void
    {
        $group = $this->createGroup(self::IDENTIFIER_GROUP);
        $kept = $this->createKey($group, 'kept');
        $written = $this->createKey($group, 'written');
        $object = $this->createObject();
        $this->mutate($object, $this->input($group, $kept, [['value' => 'kept']]));

        $this->mutate($object, $this->input($group, $written, [['value' => 'new']]));

        $keptValue = $this->store($object)->getLocalizedKeyValue($group->getId(), $kept->getKeyId(), 'default');
        $this->assertSame('kept', $keptValue);
    }

    public function testAValueLandsInTheSlotItNamesAndNowhereElse(): void
    {
        $group = $this->createGroup(self::IDENTIFIER_GROUP);
        $key = $this->createMultiValueKey($group, 'weight');
        $object = $this->createObject();

        $entries = [['value' => 'x', 'variant' => self::IMPERIAL, 'position' => 1]];
        $result = $this->mutate($object, $this->input($group, $key, $entries));

        $this->assertTrue($result['success'], $result['message']);
        $store = $this->store($object);
        $this->assertNull($store->getLocalizedKeyValue($group->getId(), $key->getKeyId(), 'default'));
        $this->assertSame('x', $store->getKeyValue($group->getId(), $key->getKeyId(), null, self::IMPERIAL, 1));
    }

    public function testAWriteToOneVariantLeavesTheOtherVariantsOfTheKeyAlone(): void
    {
        $group = $this->createGroup(self::IDENTIFIER_GROUP);
        $key = $this->createMultiValueKey($group, 'weight');
        $object = $this->createObject();
        $initial = [
            ['value' => 'm0', 'variant' => 'metric'],
            ['value' => 'm1', 'variant' => 'metric', 'position' => 1],
            ['value' => 'i0', 'variant' => self::IMPERIAL],
        ];
        $this->mutate($object, $this->input($group, $key, $initial));

        $changed = [['value' => 'i0-changed', 'variant' => self::IMPERIAL]];
        $result = $this->mutate($object, $this->input($group, $key, $changed));

        $this->assertTrue($result['success'], $result['message']);
        $store = $this->store($object);
        $groupId = $group->getId();
        $keyId = $key->getKeyId();
        $this->assertSame('i0-changed', $store->getKeyValue($groupId, $keyId, null, self::IMPERIAL, 0));
        $this->assertSame('m0', $store->getKeyValue($groupId, $keyId, null, 'metric', 0));
        $this->assertSame('m1', $store->getKeyValue($groupId, $keyId, null, 'metric', 1));
    }

    public function testAVariantTheKeyDoesNotDeclareIsRefusedAndNothingIsSaved(): void
    {
        $group = $this->createGroup(self::IDENTIFIER_GROUP);
        $multi = $this->createMultiValueKey($group, 'weight');
        $plain = $this->createKey($group, 'plain');
        $object = $this->createObject();

        foreach ([[$multi, 'imperal'], [$plain, self::IMPERIAL]] as [$key, $variant]) {
            $result = $this->mutate($object, $this->input($group, $key, [['value' => 'x', 'variant' => $variant]]));

            $this->assertFalse($result['success']);
            $this->assertStringContainsString($key->getName(), $result['message']);
            $this->assertStringContainsString($variant, $result['message']);
        }
        $this->assertSame([], $this->store($object)->getItems());
    }

    public function testTwoEntriesForOneSlotAreRefusedAndNothingIsSaved(): void
    {
        $group = $this->createGroup(self::IDENTIFIER_GROUP);
        $key = $this->createMultiValueKey($group, 'weight');
        $object = $this->createObject();
        $modified = $this->reload($object)->getModificationDate();

        $result = $this->mutate($object, $this->input($group, $key, [['value' => 'a'], ['value' => 'b']]));

        $this->assertFalse($result['success']);
        $this->assertStringContainsString($key->getName(), $result['message']);
        $this->assertSame($modified, $this->reload($object)->getModificationDate());
        $this->assertSame([], $this->store($object)->getItems());
    }

    public function testTheLanguageDefaultsToDefaultAndAGivenLanguageTouchesOnlyItself(): void
    {
        $group = $this->createGroup(self::IDENTIFIER_GROUP);
        $key = $this->createKey($group, 'localized');
        $object = $this->createObject();

        $this->mutate($object, $this->input($group, $key, [['value' => 'plain']]));
        $this->mutate($object, $this->input($group, $key, [['value' => 'deutsch']], 'de'));

        $items = $this->store($object)->getItems();
        $this->assertSame('plain', $items[$group->getId()][$key->getKeyId()]['default']);
        $this->assertSame('deutsch', $items[$group->getId()][$key->getKeyId()]['de']);
    }

    public function testADisabledKeyRefusesTheWholeMutationButStaysReadable(): void
    {
        $group = $this->createGroup(self::IDENTIFIER_GROUP);
        $enabled = $this->createKey($group, 'enabled_key');
        $disabled = $this->createKey($group, 'disabled_key');
        $object = $this->createObject();
        $this->mutate($object, $this->input($group, $disabled, [['value' => 'stored']]));
        // the flag lives on the key, and disabling a key drops its relations: the assignment is restored after it
        $keyConfig = KeyConfig::getById($disabled->getKeyId(), true);
        $keyConfig->setEnabled(false);
        $keyConfig->save();
        $disabled->save();

        $result = $this->mutate($object, ['groups' => [['groupId' => $group->getId(), 'keys' => [
            ['keyId' => $enabled->getKeyId(), 'values' => [['value' => 'new']]],
            ['keyId' => $disabled->getKeyId(), 'values' => [['value' => 'new']]],
        ]]]]);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString($disabled->getName(), $result['message']);
        $this->assertStringContainsString('disabled', $result['message']);
        $this->assertStringContainsString('Nothing was saved', $result['message']);
        $store = $this->store($object);
        $this->assertNull($store->getLocalizedKeyValue($group->getId(), $enabled->getKeyId(), 'default'));

        $service = $this->createServiceStub();
        $service->setSupportedCsFeatureQueryDataTypes(self::READABLE_TYPES);
        $resolve = (new Group($service, new Feature($service)))->getField('features')->resolveFn;
        $features = $resolve(['id' => $group->getId(), '_csValue' => $store, '_language' => 'default']);
        $byKey = [];
        foreach ($features as $feature) {
            $byKey[$feature->getId()] = $feature->getValue();
        }
        $this->assertSame('stored', $byKey[$disabled->getKeyId()]);
    }

    public function testAGroupOutsideTheAllowedListIsRefusedAndAnEmptyListMeansUnrestricted(): void
    {
        $allowed = $this->createGroup('allowed_group');
        $other = $this->createGroup('other_group');
        $allowedKey = $this->createKey($allowed, 'ak');
        $otherKey = $this->createKey($other, 'ok');

        $restricted = $this->createObject([$allowed->getId()]);
        $result = $this->mutate($restricted, $this->input($other, $otherKey, [['value' => 'x']]));
        $this->assertFalse($result['success']);
        $this->assertStringContainsString($other->getName(), $result['message']);
        $this->assertSame([], $this->store($restricted)->getItems());

        $allowedInput = $this->input($allowed, $allowedKey, [['value' => 'x']]);
        $this->assertTrue($this->mutate($restricted, $allowedInput)['success']);

        $unrestricted = $this->createObject();
        $this->assertTrue($this->mutate($unrestricted, $this->input($other, $otherKey, [['value' => 'y']]))['success']);
        $otherValue = $this->store($unrestricted)
            ->getLocalizedKeyValue($other->getId(), $otherKey->getKeyId(), 'default');
        $this->assertSame('y', $otherValue);
    }

    public function testAGroupOfAForeignStoreIsRefused(): void
    {
        $foreignStore = $this->createStore();
        $this->createGroup('home_group');
        $foreign = $this->createGroup('foreign_group', $foreignStore);
        $foreignKey = $this->createKey($foreign, 'fk');
        $object = $this->createObject();

        $result = $this->mutate($object, $this->input($foreign, $foreignKey, [['value' => 'x']]));
        $foreignStore->delete();

        $this->assertFalse($result['success']);
        $this->assertStringContainsString($foreign->getName(), $result['message']);
    }

    public function testAnInactiveGroupIsActivated(): void
    {
        $group = $this->createGroup(self::IDENTIFIER_GROUP);
        $key = $this->createKey($group, 'k');
        $object = $this->createObject();
        $this->assertArrayNotHasKey($group->getId(), $this->store($object)->getActiveGroups());

        $result = $this->mutate($object, $this->input($group, $key, [['value' => 'x']]));

        $this->assertTrue($result['success'], $result['message']);
        $this->assertTrue($this->store($object)->getActiveGroups()[$group->getId()]);
    }

    public function testStrictRefusesAnInactiveGroup(): void
    {
        $group = $this->createGroup(self::IDENTIFIER_GROUP);
        $key = $this->createKey($group, 'k');
        $object = $this->createObject();

        $input = $this->input($group, $key, [['value' => 'x']]);
        $input['strict'] = true;
        $result = $this->mutate($object, $input);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString($group->getName(), $result['message']);
        $store = $this->store($object);
        $this->assertArrayNotHasKey($group->getId(), $store->getActiveGroups());
        $this->assertNull($store->getLocalizedKeyValue($group->getId(), $key->getKeyId(), 'default'));
    }
}
