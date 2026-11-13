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

namespace Pimcore\Bundle\DataHubBundle\Tests\GraphQL\ClassificationstoreType;

use Codeception\Test\Unit;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreType\Feature;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreType\Group;
use Pimcore\Bundle\DataHubBundle\Tests\GraphQL\DataObjectInputProcessor\ClassificationstoreFixtureTrait;
use Pimcore\Model\DataObject\Classificationstore\GroupConfig;
use Pimcore\Model\DataObject\Concrete;

/**
 * @internal
 */
class QualifiedViewTest extends Unit
{
    use ClassificationstoreFixtureTrait {
        deleteFixtures as protected _after;
    }

    public function testEveryQualifierRoundTripsAndFeaturesKeepsTheDefaultSlot(): void
    {
        $group = $this->createGroup('qualified_group');
        $key = $this->createMultiValueKey($group, 'weight');
        $object = $this->createObject();
        $values = [
            ['value' => 'm0'],
            ['value' => 'm1', 'position' => 1],
            ['value' => 'i0', 'variant' => 'imperial'],
        ];
        $this->mutate($object, $this->input($group, $key, $values));

        $service = $this->createServiceStub();
        $service->setSupportedCsFeatureQueryDataTypes(self::READABLE_TYPES);
        $type = new Group($service, new Feature($service));
        $value = ['id' => $group->getId(), '_csValue' => $this->store($object), '_language' => 'default'];

        $qualified = $type->getField('qualifiedFeatures')->resolveFn;
        $entries = [];
        $valuesOf = $type->getField('qualifiedFeatures')->getType()->getWrappedType()->getField('values')->resolveFn;
        foreach ($qualified($value) as $feature) {
            foreach ($valuesOf($feature) as $entry) {
                $entries[] = [$entry['variant'], $entry['position'], $entry['value']->getValue()];
            }
        }
        $this->assertEqualsCanonicalizing(
            [['default', 0, 'm0'], ['default', 1, 'm1'], ['imperial', 0, 'i0']],
            $entries
        );

        $features = $type->getField('features')->resolveFn;
        $this->assertSame(['m0'], array_map(static fn ($f) => $f->getValue(), $features($value)));
    }

    public function testQualifiedFeaturesOfANonLocalizedStoreReadTheDefaultBucketWithoutALanguage(): void
    {
        $group = $this->createGroup('plain_group');
        $key = $this->createMultiValueKey($group, 'plain_weight');
        $object = $this->createObject([], false);
        $this->mutate($object, $this->input($group, $key, [['value' => 'a'], ['value' => 'b', 'position' => 1]]));

        $service = $this->createServiceStub();
        $service->setSupportedCsFeatureQueryDataTypes(self::READABLE_TYPES);
        $type = new Group($service, new Feature($service));
        $qualified = $type->getField('qualifiedFeatures')->resolveFn;
        $valuesOf = $type->getField('qualifiedFeatures')->getType()->getWrappedType()->getField('values')->resolveFn;

        // no language argument, and a named one that the field does not keep
        foreach ([null, 'en'] as $language) {
            $value = ['id' => $group->getId(), '_csValue' => $this->store($object), '_language' => $language];
            $entries = [];
            foreach ($qualified($value) as $feature) {
                foreach ($valuesOf($feature) as $entry) {
                    $entries[] = [$entry['position'], $entry['value']->getValue()];
                }
            }
            $this->assertEqualsCanonicalizing([[0, 'a'], [1, 'b']], $entries, (string) $language);
        }
    }

    public function testAKeyFlaggedLocalizedInANonLocalizedFieldRoundTripsUnderANamedLanguage(): void
    {
        $group = $this->createGroup('per_key_group');
        $key = $this->createKey($group, 'per_key', 'input', [], true, false, true);
        $object = $this->createObject([], false);

        $written = $this->mutate($object, $this->input($group, $key, [['value' => 'deutsch']], 'de'));
        $this->assertTrue($written['success'], $written['message']);

        $this->assertSame([[0, 'deutsch']], $this->readQualified($object, $group, 'de'));
        $this->assertSame(['deutsch'], $this->readFeatures($object, $group, 'de'));
    }

    public function testAKeyFlaggedNotLocalizedInALocalizedFieldIsWrittenAndReadOnce(): void
    {
        $group = $this->createGroup('per_key_plain_group');
        $key = $this->createKey($group, 'per_key_plain', 'input', [], true, false, false);
        $object = $this->createObject();

        $refused = $this->mutate($object, $this->input($group, $key, [['value' => 'x']], 'de'));
        $this->assertFalse($refused['success']);

        $written = $this->mutate($object, $this->input($group, $key, [['value' => 'once']]));
        $this->assertTrue($written['success'], $written['message']);

        // whatever language the query names, the key answers from the non-localized bucket, once
        foreach (['de', 'en', null] as $language) {
            $this->assertSame([[0, 'once']], $this->readQualified($object, $group, $language), (string) $language);
            $this->assertSame(['once'], $this->readFeatures($object, $group, $language), (string) $language);
        }
    }

    /**
     * @return array<int, array{0: int, 1: mixed}> position and value of every qualified value of the group
     */
    private function readQualified(Concrete $object, GroupConfig $group, ?string $language): array
    {
        $service = $this->createServiceStub();
        $service->setSupportedCsFeatureQueryDataTypes(self::READABLE_TYPES);
        $type = new Group($service, new Feature($service));
        $value = ['id' => $group->getId(), '_csValue' => $this->store($object), '_language' => $language];
        $valuesOf = $type->getField('qualifiedFeatures')->getType()->getWrappedType()->getField('values')->resolveFn;

        $entries = [];
        foreach (($type->getField('qualifiedFeatures')->resolveFn)($value) as $feature) {
            foreach ($valuesOf($feature) as $entry) {
                $entries[] = [$entry['position'], $entry['value']->getValue()];
            }
        }

        return $entries;
    }

    /**
     * @return mixed[]
     */
    private function readFeatures(Concrete $object, GroupConfig $group, ?string $language): array
    {
        $service = $this->createServiceStub();
        $service->setSupportedCsFeatureQueryDataTypes(self::READABLE_TYPES);
        $type = new Group($service, new Feature($service));
        $value = ['id' => $group->getId(), '_csValue' => $this->store($object), '_language' => $language];

        return array_map(
            static fn ($feature) => $feature->getValue(),
            ($type->getField('features')->resolveFn)($value)
        );
    }

    public function testTheGroupFieldMapOnlyGainedTheQualifiedView(): void
    {
        $service = $this->createServiceStub();
        $type = new Group($service, new Feature($service));

        $this->assertSame(
            ['id', 'identifier', 'name', 'description', 'features', 'qualifiedFeatures'],
            array_keys($type->getFields())
        );
    }
}
