<?php

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

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreFeatureType\Helper;
use Pimcore\Bundle\DataHubBundle\GraphQL\FeatureDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Model\DataObject\ClassDefinition\Data\Classificationstore as ClassificationstoreDefinition;
use Pimcore\Model\DataObject\Classificationstore;
use Pimcore\Model\DataObject\Classificationstore\GroupConfig;
use Pimcore\Model\DataObject\Classificationstore\KeyGroupRelation;
use Pimcore\Model\DataObject\Concrete;

/**
 * @internal
 */
final class Group extends ObjectType
{
    use ServiceTrait;

    /** @var Feature */
    protected $featureType;

    /** @var QualifiedFeature */
    protected $qualifiedFeatureType;

    /**
     * @param array $config
     * @param array $context
     */
    public function __construct(Service $graphQlService, Feature $featuresType, $config = ['name' => 'csGroup'], $context = [])
    {
        $this->setGraphQLService($graphQlService);
        $this->featureType = $featuresType;
        $this->qualifiedFeatureType = new QualifiedFeature($graphQlService);
        $this->build($config);
        parent::__construct($config);
    }

    /**
     * @param array $config
     */
    public function build(&$config)
    {
        $resolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\AssetType();
        $resolver->setGraphQLService($this->getGraphQlService());

        $config['fields'] = [
            'id' => [
                'type' => Type::int(),
                'deprecationReason' => Helper::getIdDeprecationReason(),
            ],
            'identifier' => [
                'type' => Type::string(),
                'resolve' => static function (mixed $value): ?string {
                    $groupConfig = GroupConfig::getById($value['id']);
                    if ($groupConfig) {
                        return $groupConfig->getIdentifier();
                    }

                    return null;
                },
            ],
            'name' => Type::string(),
            'description' => Type::string(),
            'features' => [
                'type' => Type::listOf($this->featureType),
                'resolve' => function ($value) {
                    /** @var Classificationstore $csValue */
                    $csValue = $value['_csValue'];
                    $groupId = $value['id'];

                    $result = [];

                    $service = $this->getGraphQlService();
                    $supportedFeatureTypeNames = $service->getSupportedCsFeatureQueryDataTypes();

                    foreach ($this->loadKeyRelations($groupId) as $keyRelation) {
                        if ($this->isPublished($keyRelation, $supportedFeatureTypeNames)) {
                            $keyId = $keyRelation->getKeyId();
                            $language = $this->resolveLanguage($value, $keyId);
                            //TODO maybe add args for this fallback stuff ?

                            $featureValue = $csValue->getLocalizedKeyValue($groupId, $keyId, $language);
                            $result[] = $this->createDescriptor(
                                $csValue,
                                $groupId,
                                $language,
                                $keyRelation,
                                $featureValue
                            );
                        }
                        // a key of a type no read generator publishes is skipped, not answered with null
                    }

                    return $result;
                },
            ],
            'qualifiedFeatures' => [
                'type' => Type::listOf($this->qualifiedFeatureType),
                'description' => 'the keys of the group with all of their qualified values '
                    . '(language, variant, position)',
                'resolve' => function ($value) {
                    /** @var Classificationstore $csValue */
                    $csValue = $value['_csValue'];
                    QualifiedValueReader::assertSupported($csValue);

                    $groupId = $value['id'];
                    $supportedFeatureTypeNames = $this->getGraphQlService()->getSupportedCsFeatureQueryDataTypes();

                    $result = [];
                    foreach ($this->loadKeyRelations($groupId) as $keyRelation) {
                        if ($this->isPublished($keyRelation, $supportedFeatureTypeNames)) {
                            $language = $this->resolveLanguage($value, $keyRelation->getKeyId());
                            $result[] = $this->createDescriptor($csValue, $groupId, $language, $keyRelation, null);
                        }
                    }

                    return $result;
                },
            ],
        ];
    }

    private function resolveLanguage(array $value, int $keyId): string
    {
        // a key that is not kept per language holds all of its values in the 'default' bucket, whatever language
        // the query or the request names, as `features` does through getLocalizedKeyValue(); the key decides for
        // itself, and only a key that does not inherits the flag of its field
        if (!QualifiedValueReader::isKeyLocalized($keyId, $this->isLocalizedField($value['_csValue']))) {
            return 'default';
        }

        $language = $value['_language'];
        if (!$language) {
            // Let's try to "inherit" the language from what's already been parsed from this query
            $language = $this->getGraphQlService()->getLocaleService()->getLocale();
            if (!$language) {
                $language = 'default';
            }
        }

        return $language;
    }

    private function isLocalizedField(Classificationstore $csValue): bool
    {
        $object = $csValue->getObject();
        $definition = $object instanceof Concrete
            ? $object->getClass()->getFieldDefinition($csValue->getFieldname())
            : null;

        return !$definition instanceof ClassificationstoreDefinition || $definition->isLocalized();
    }

    /**
     * @return KeyGroupRelation[]
     */
    private function loadKeyRelations(int $groupId): array
    {
        $keyRelations = new Classificationstore\KeyGroupRelation\Listing();
        $keyRelations->setCondition('groupId = ' . $groupId);

        return $keyRelations->load();
    }

    /**
     * @param string[] $supportedFeatureTypeNames
     */
    private function isPublished(KeyGroupRelation $keyRelation, array $supportedFeatureTypeNames): bool
    {
        return in_array(QualifiedValueReader::publishedType($keyRelation), $supportedFeatureTypeNames, true);
    }

    private function createDescriptor(
        Classificationstore $csValue,
        int $groupId,
        string $language,
        KeyGroupRelation $keyRelation,
        mixed $featureValue
    ): FeatureDescriptor {
        $wrappedFeatureValue = new FeatureDescriptor();
        $wrappedFeatureValue->setId($keyRelation->getKeyId());
        $wrappedFeatureValue->setType($keyRelation->getType());
        $wrappedFeatureValue->setValue($featureValue);
        $wrappedFeatureValue->setStore($csValue);
        $wrappedFeatureValue->setGroupId($groupId);
        $wrappedFeatureValue->setLanguage($language);
        $wrappedFeatureValue->setRelation($keyRelation);

        return $wrappedFeatureValue;
    }
}
