<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreType;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\FeatureDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Model\DataObject\Classificationstore;

class Group extends ObjectType
{
    use ServiceTrait;

    /** @var Feature */
    protected $featureType;

    /**
     * @param Service $graphQlService
     * @param Feature $featuresType
     * @param array $config
     * @param array $context
     */
    public function __construct(Service $graphQlService, Feature $featuresType, $config = ['name' => 'csGroup'], $context = [])
    {
        $this->setGraphQLService($graphQlService);
        $this->featureType = $featuresType;
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
            'id' => Type::int(),
            'name' => Type::string(),
            'description' => Type::string(),
            'features' => [
                'type' => Type::listOf($this->featureType),
                'resolve' => function ($value, $args, $context = [], ResolveInfo $resolveInfo = null) {
                    /** @var Classificationstore $csValue */
                    $csValue = $value['_csValue'];
                    $groupId = $value['id'];
                    $language = $value['_language'];
                    if (!$language) {
                        // Let's try to "inherit" the language from what's already been parsed from this query
                        $language = $this->getGraphQlService()->getLocaleService()->getLocale();
                        if (!$language) {
                            $language = 'default';
                        }
                    }

                    $keyRelations = new Classificationstore\KeyGroupRelation\Listing();
                    $keyRelations->setCondition('groupId = ' . $groupId);
                    $keyRelations = $keyRelations->load();

                    $result = [];

                    $service = $this->getGraphQlService();
                    $supportedFeatureTypeNames = $service->getSupportedCsFeatureQueryDataTypes();

                    foreach ($keyRelations as $keyRelation) {
                        $keyDataType = $keyRelation->getType();
                        if (in_array($keyDataType, $supportedFeatureTypeNames)) {
                            $keyId = $keyRelation->getKeyId();
                            //TODO maybe add args for this fallback stuff ?

                            $featureValue = $csValue->getLocalizedKeyValue($groupId, $keyId, $language);
                            $wrappedFeatureValue = new FeatureDescriptor();
                            $wrappedFeatureValue->setId($keyId);
                            $wrappedFeatureValue->setType($keyDataType);
                            $wrappedFeatureValue->setValue($featureValue);
                            $result[] = $wrappedFeatureValue;
                        }
                        //TODO decide whether we want to skip unsupported types (as we do now) or simply add null
                    }

                    return $result;
                }
            ]
        ];
    }
}
