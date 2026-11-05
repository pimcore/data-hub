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
use GraphQL\GraphQL;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreFeatureType\BooleanSelectType;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreFeatureType\CheckboxType;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreFeatureType\DateType;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreFeatureType\Helper;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreFeatureType\StringType;
use Pimcore\Bundle\DataHubBundle\GraphQL\FeatureDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\TypeInterface\CsFeature;
use Pimcore\Model\DataObject\Classificationstore\KeyConfig;
use Pimcore\Model\DataObject\Classificationstore\StoreConfig;

/**
 * The key identifier is published next to the deprecated integer `id` on the csFeature* object types
 * and on csFeatureInterface. Every csFeature* type merges the same field map
 * (Helper::getCommonFields(), see ClassificationstoreFeatureType/StringType.php:35), so the map is
 * asserted directly; the per-type assertions cover the types that build without a wired Service -
 * MultiselectType and QuantityValueType take one (MultiselectType.php:36, QuantityValueType.php:34)
 * and merge the very same map.
 */
class FeatureIdentifierTest extends Unit
{
    private const ID = 'id';

    private const IDENTIFIER = 'identifier';

    private const CHECKBOX_TYPE = 'csFeatureCheckbox';

    /**
     * No key config is saved under this id, so KeyConfig::getById() answers with null.
     */
    private const UNRESOLVABLE_KEY_ID = 999999999;

    /**
     * @var KeyConfig[]
     */
    private array $keyConfigs = [];

    private ?StoreConfig $store = null;

    protected function _after(): void
    {
        foreach ($this->keyConfigs as $keyConfig) {
            $keyConfig->delete();
        }
        $this->keyConfigs = [];

        $this->store?->delete();
        $this->store = null;
    }

    public function testCommonFieldsPublishIdentifierAsNullableStringNextToTheDeprecatedId(): void
    {
        $fields = Helper::getCommonFields();

        $this->assertSame(Type::string(), $fields[self::IDENTIFIER]['type']);
        $this->assertArrayNotHasKey('deprecationReason', $fields[self::IDENTIFIER]);

        $this->assertSame(Type::int(), $fields[self::ID]['type']);
        $this->assertSame(Helper::ID_DEPRECATION_REASON, $fields[self::ID]['deprecationReason']);
    }

    public function testTheDeprecationReasonNamesTheReplacementAndTheRemovalRelease(): void
    {
        $this->assertStringContainsString(self::IDENTIFIER, Helper::ID_DEPRECATION_REASON);
        $this->assertStringContainsString('2027.1', Helper::ID_DEPRECATION_REASON);
    }

    public function testTheFeatureTypesBuiltWithoutAServicePublishIdentifierAndKeepIdAsADeprecatedInt(): void
    {
        foreach ($this->featureTypes() as $type) {
            $identifier = $type->getField(self::IDENTIFIER);
            $this->assertSame(Type::string(), $identifier->getType(), $type->name);
            $this->assertFalse($identifier->isDeprecated(), $type->name);

            $id = $type->getField(self::ID);
            $this->assertSame(Type::int(), $id->getType(), $type->name);
            $this->assertTrue($id->isDeprecated(), $type->name);
            $this->assertSame(Helper::ID_DEPRECATION_REASON, $id->deprecationReason, $type->name);
        }
    }

    /**
     * The interface and the object types have to agree: a field deprecated on one and not on the
     * other shows the same field as deprecated and not deprecated in a single introspection pass.
     */
    public function testTheInterfaceDeclaresIdAndIdentifierExactlyLikeTheObjectTypes(): void
    {
        $interface = CsFeature::getInstance();
        $commonFields = Helper::getCommonFields();

        foreach ([self::ID, self::IDENTIFIER] as $fieldName) {
            $interfaceField = $interface->getField($fieldName);

            $this->assertSame($commonFields[$fieldName]['type'], $interfaceField->getType(), $fieldName);
            $this->assertSame(
                $commonFields[$fieldName]['deprecationReason'] ?? null,
                $interfaceField->deprecationReason,
                $fieldName
            );
        }
    }

    public function testIntrospectionReportsTheDeprecatedIdOnlyWithIncludeDeprecated(): void
    {
        $schema = $this->buildSchema();
        $schema->assertValid();

        $withDeprecated = $this->introspectFields($schema, true);
        $this->assertTrue($withDeprecated[self::ID]['isDeprecated']);
        $this->assertSame(Helper::ID_DEPRECATION_REASON, $withDeprecated[self::ID]['deprecationReason']);
        $this->assertFalse($withDeprecated[self::IDENTIFIER]['isDeprecated']);
        $this->assertNull($withDeprecated[self::IDENTIFIER]['deprecationReason']);

        $default = $this->introspectFields($schema, false);
        $this->assertArrayNotHasKey(self::ID, $default);
        $this->assertArrayHasKey(self::IDENTIFIER, $default);
    }

    public function testIdentifierResolvesToTheIdentifierOfTheKeyWhileIdKeepsResolvingToTheKeyId(): void
    {
        $identifier = 'datahub_feature_identifier';
        $keyId = $this->createKeyConfig($identifier);
        $descriptor = $this->createDescriptor($keyId);

        $this->assertSame($identifier, $this->resolve(self::IDENTIFIER, $descriptor));
        $this->assertSame($keyId, $this->resolve(self::ID, $descriptor));
    }

    public function testIdentifierResolvesToNullForAKeyWithoutAnIdentifier(): void
    {
        $descriptor = $this->createDescriptor($this->createKeyConfig(null));

        $this->assertNull($this->resolve(self::IDENTIFIER, $descriptor));
    }

    public function testIdentifierResolvesToNullWhenTheKeyConfigDoesNotResolve(): void
    {
        $descriptor = $this->createDescriptor(self::UNRESOLVABLE_KEY_ID);

        $this->assertNull($this->resolve(self::IDENTIFIER, $descriptor));
    }

    /**
     * @return ObjectType[]
     */
    private function featureTypes(): array
    {
        return [
            CheckboxType::getInstance(),
            BooleanSelectType::getInstance(),
            DateType::getInstance(),
            StringType::getInstance('csFeatureInput', 'text'),
        ];
    }

    private function buildSchema(): Schema
    {
        $fields = [];
        foreach ($this->featureTypes() as $type) {
            $fields[$type->name] = ['type' => $type];
        }

        return new Schema([
            'query' => new ObjectType([
                'name' => 'FeatureIdentifierTestQuery',
                'fields' => $fields,
            ]),
            'types' => [CsFeature::getInstance()],
        ]);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function introspectFields(Schema $schema, bool $includeDeprecated): array
    {
        $result = GraphQL::executeQuery(
            $schema,
            'query ($name: String!, $includeDeprecated: Boolean!) {
                __type(name: $name) {
                    fields(includeDeprecated: $includeDeprecated) {
                        name
                        isDeprecated
                        deprecationReason
                    }
                }
            }',
            null,
            null,
            ['name' => self::CHECKBOX_TYPE, 'includeDeprecated' => $includeDeprecated]
        )->toArray();

        $this->assertArrayNotHasKey('errors', $result);

        $fields = [];
        foreach ($result['data']['__type']['fields'] as $field) {
            $fields[$field['name']] = $field;
        }

        return $fields;
    }

    private function resolve(string $fieldName, FeatureDescriptor $descriptor): mixed
    {
        $resolve = CheckboxType::getInstance()->getField($fieldName)->resolveFn;

        return $resolve($descriptor);
    }

    private function createDescriptor(int $keyId): FeatureDescriptor
    {
        $descriptor = new FeatureDescriptor();
        $descriptor->setId($keyId);
        $descriptor->setType('checkbox');

        return $descriptor;
    }

    private function createKeyConfig(?string $identifier): int
    {
        if ($this->store === null) {
            $this->store = new StoreConfig();
            $this->store->setName('datahub-feature-identifier-' . uniqid());
            $this->store->save();
        }

        $keyConfig = new KeyConfig();
        $keyConfig->setStoreId($this->store->getId());
        $keyConfig->setName('datahub-feature-identifier-' . uniqid());
        $keyConfig->setType('checkbox');
        $keyConfig->setIdentifier($identifier);
        $keyConfig->save();

        $this->keyConfigs[] = $keyConfig;

        return $keyConfig->getId();
    }
}
