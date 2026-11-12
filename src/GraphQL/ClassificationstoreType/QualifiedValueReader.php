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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreType;

use Pimcore\Bundle\DataHubBundle\GraphQL\Exception\ClientSafeException;
use Pimcore\Bundle\DataHubBundle\GraphQL\FeatureDescriptor;
use Pimcore\Model\DataObject\ClassDefinition\Data\MultiValue as MultiValueDefinition;
use Pimcore\Model\DataObject\Classificationstore;
use Pimcore\Model\DataObject\Classificationstore\Engine\EngineFeature;
use Pimcore\Model\DataObject\Classificationstore\KeyGroupRelation;
use Pimcore\Model\DataObject\Classificationstore\Service as ClassificationstoreService;

/**
 * Reads the qualified values (language, variant, position) of one key for the types that publish them.
 *
 * @internal
 */
final class QualifiedValueReader
{
    private const DEFAULT_VARIANT = 'default';

    /**
     * Whether the engine behind the store can hold variants and positions above 0.
     */
    public static function supportsQualifiers(Classificationstore $store): bool
    {
        // a store with no class has no storage to resolve an engine from, core skips its check the same way
        if ($store->getClass() === null) {
            return true;
        }

        $dao = $store->getDao();

        return $dao->supports(EngineFeature::MultiValue) && $dao->supports(EngineFeature::Variants);
    }

    /**
     * @throws ClientSafeException
     */
    public static function assertSupported(Classificationstore $store): void
    {
        if (!self::supportsQualifiers($store)) {
            throw new ClientSafeException(sprintf(
                'The storage engine of the classification store "%s" does not support qualified values'
                . ' (variants and positions), change the engine setting of its store or use `features`.',
                $store->getFieldname()
            ));
        }
    }

    /**
     * The values of a multiValue key: all qualified values, or the single unqualified one on an engine
     * that cannot hold qualified values.
     *
     * @return array<int, array{language: ?string, variant: ?string, position: ?int, value: FeatureDescriptor}>
     *
     * @throws \Exception
     */
    public static function entries(FeatureDescriptor $descriptor): array
    {
        $store = $descriptor->getStore();
        if ($store === null || $descriptor->getRelation() === null) {
            throw new ClientSafeException(
                'The values of this classification store key cannot be read: the key is not bound to a store.'
            );
        }

        $valueType = self::getValueType($descriptor);
        if ($valueType === null) {
            return [];
        }

        if (!self::supportsQualifiers($store)) {
            $value = $store->getLocalizedKeyValue(
                $descriptor->getGroupId(),
                $descriptor->getId(),
                $descriptor->getLanguage()
            );

            return [self::entry($descriptor, $valueType, null, null, null, $value)];
        }

        return self::qualifiedEntries($descriptor);
    }

    /**
     * @return array<int, array{language: ?string, variant: ?string, position: ?int, value: FeatureDescriptor}>
     *
     * @throws \Exception
     */
    public static function qualifiedEntries(FeatureDescriptor $descriptor): array
    {
        $store = $descriptor->getStore();
        self::assertSupported($store);

        $definition = ClassificationstoreService::getFieldDefinitionFromKeyConfig($descriptor->getRelation());
        $variants = [self::DEFAULT_VARIANT];
        if ($definition instanceof MultiValueDefinition) {
            $variants = array_merge($variants, $definition->getVariantNames());
        }

        $valueType = self::getValueType($descriptor);
        if ($valueType === null) {
            return [];
        }

        $result = [];
        foreach ($variants as $variant) {
            $values = $store->getKeyValues(
                $descriptor->getGroupId(),
                $descriptor->getId(),
                $descriptor->getLanguage(),
                (string) $variant
            );
            foreach ($values as $position => $value) {
                $result[] = self::entry(
                    $descriptor,
                    $valueType,
                    $descriptor->getLanguage(),
                    (string) $variant,
                    (int) $position,
                    $value
                );
            }
        }

        return $result;
    }

    /**
     * The type a value of the key is published as: the declared inner type of a wrapper key, the key's own
     * type otherwise; null for a wrapper that names no inner type, which has no type to publish its values as.
     */
    public static function publishedType(KeyGroupRelation $relation): ?string
    {
        $definition = ClassificationstoreService::getFieldDefinitionFromKeyConfig($relation);
        if (!$definition instanceof MultiValueDefinition) {
            return $relation->getType();
        }

        $inner = ClassificationstoreService::getValueDefinition($definition);

        return $inner === null || $inner instanceof MultiValueDefinition ? null : $inner->getFieldtype();
    }

    private static function getValueType(FeatureDescriptor $descriptor): ?string
    {
        return self::publishedType($descriptor->getRelation());
    }

    /**
     * @return array{language: ?string, variant: ?string, position: ?int, value: FeatureDescriptor}
     */
    private static function entry(
        FeatureDescriptor $key,
        string $valueType,
        ?string $language,
        ?string $variant,
        ?int $position,
        mixed $value
    ): array {
        $wrapped = new FeatureDescriptor();
        $wrapped->setId($key->getId());
        $wrapped->setType($valueType);
        $wrapped->setValue($value);

        return ['language' => $language, 'variant' => $variant, 'position' => $position, 'value' => $wrapped];
    }
}
