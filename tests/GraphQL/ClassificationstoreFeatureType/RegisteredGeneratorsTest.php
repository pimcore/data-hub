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

namespace Pimcore\Bundle\DataHubBundle\Tests\GraphQL\ClassificationstoreFeatureType;

use Codeception\Test\Unit;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\Tests\GraphQL\ServiceStubTrait;
use Symfony\Component\Yaml\Yaml;

/**
 * The Classification Store read generators are derived from the plain field read generators, both
 * read off the service tags (DependencyInjection/Compiler/ImportExportLocatorsPass.php:382-415).
 * A type added to either side fails here instead of silently missing from the csFeature union.
 *
 * @internal
 */
class RegisteredGeneratorsTest extends Unit
{
    use ServiceStubTrait;

    private const PLAIN_TAG = 'pimcore.datahub.graphql.dataobjectquerytypegenerator';

    private const CS_TAG = 'pimcore.datahub.graphql.csfeaturequerytypegenerator';

    private const PLAIN_PREFIX = 'typegenerator_dataobjectquerydatatype_';

    private const CS_PREFIX = 'typegenerator_csfeaturequerydatatype_';

    private const CONTAINER_TYPE = 'container type';

    /**
     * Plain field types without a Classification Store read generator, with the reason.
     */
    private const EXCLUSIONS = [
        'block' => self::CONTAINER_TYPE,
        'fieldcollections' => self::CONTAINER_TYPE,
        'classificationstore' => self::CONTAINER_TYPE,
        'reverseObjectRelation' => 'computed from the owning side, not stored on a key',
        'reverseManyToManyObjectRelation' => 'computed from the owning side, not stored on a key',
        'urlSlug' => 'needs a route registration keyed to the object and field name',
        'table' => 'its fields come from the columns of the field definition, not of a shared type',
        'structuredTable' => 'its fields come from the columns of the field definition, not of a shared type',
        'href' => 'legacy id of manyToOneRelation, a key reports the modern name',
        'multihref' => 'legacy id of manyToManyRelation, a key reports the modern name',
        'multihrefMetadata' => 'legacy id of advancedManyToManyRelation, a key reports the modern name',
        'newsletterActive' => 'customer management framework type, not a core field type',
        'newsletterConfirmed' => 'customer management framework type, not a core field type',
    ];

    public function testEveryPlainFieldTypeHasAClassificationStoreGeneratorOrIsExcluded(): void
    {
        $plain = $this->taggedTypes(self::PLAIN_TAG, self::PLAIN_PREFIX);
        $cs = $this->taggedTypes(self::CS_TAG, self::CS_PREFIX);

        $missing = array_values(array_diff($plain, $cs));
        sort($missing);
        $excluded = array_keys(self::EXCLUSIONS);
        sort($excluded);

        $this->assertSame($excluded, $missing);
    }

    public function testNoClassificationStoreGeneratorExistsForATypeWithoutAPlainFieldGenerator(): void
    {
        $plain = $this->taggedTypes(self::PLAIN_TAG, self::PLAIN_PREFIX);
        $cs = $this->taggedTypes(self::CS_TAG, self::CS_PREFIX);

        $this->assertSame([], array_values(array_diff($cs, $plain)));
    }

    public function testEveryGeneratorPublishesItsOwnFeatureTypeName(): void
    {
        $names = [];
        foreach ($this->services() as $definition) {
            foreach ($definition['tags'] ?? [] as $tag) {
                if ($tag['name'] !== self::CS_TAG) {
                    continue;
                }
                $generator = new ($definition['class'])($this->createServiceWithDataObjectTypes());
                $names[] = $generator->getFieldType()->name;
            }
        }

        $this->assertSame(array_unique($names), $names);
        $this->assertCount(count($this->taggedTypes(self::CS_TAG, self::CS_PREFIX)), $names);
    }

    /**
     * The generators of the relation, media and hotspot types build on the data object types the service registers
     * (getDataObjectTypeDefinition()); this one answers every requested name with a minimal object type.
     */
    private function createServiceWithDataObjectTypes(): Service
    {
        $service = $this->createServiceStub();
        $types = new class () extends \ArrayObject {
            public function offsetExists(mixed $key): bool
            {
                return true;
            }

            public function offsetGet(mixed $key): mixed
            {
                return new ObjectType(['name' => 'stub_' . $key, 'fields' => ['id' => Type::int()]]);
            }
        };

        \Closure::bind(static function (Service $service, \ArrayObject $types): void {
            $service->dataObjectDataTypes = $types;
        }, null, Service::class)($service, $types);

        return $service;
    }

    /**
     * @return string[]
     */
    private function taggedTypes(string $tagName, string $prefix): array
    {
        $types = [];
        foreach ($this->services() as $definition) {
            foreach ($definition['tags'] ?? [] as $tag) {
                if ($tag['name'] === $tagName && str_starts_with($tag['id'], $prefix)) {
                    $types[] = substr($tag['id'], strlen($prefix));
                }
            }
        }

        return $types;
    }

    private function services(): array
    {
        $config = Yaml::parseFile(__DIR__ . '/../../../src/Resources/config/graphql.yml');

        return $config['services'];
    }
}
