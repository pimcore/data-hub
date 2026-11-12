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

/**
 * @internal
 */
class QualifiedViewTest extends Unit
{
    use ClassificationstoreFixtureTrait;

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
