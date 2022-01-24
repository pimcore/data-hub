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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassTypeDefinitions;
use Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\ObjectMetadata;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Fieldcollection\Definition as FieldcollectionDefinition;
use Pimcore\Model\DataObject\Objectbrick\Definition as ObjectbrickDefinition;

class ObjectMetadataType extends ObjectType
{
    use ServiceTrait;

    protected $class;

    /** @var Data */
    protected $fieldDefinition;

    /**
     * @param Service $graphQlService
     * @param Data|null $fieldDefinition
     * @param ClassDefinition|null $class
     * @param array $config
     */
    public function __construct(Service $graphQlService, Data $fieldDefinition = null, $class = null, $config = [])
    {
        $this->setGraphQLService($graphQlService);
        $this->class = $class;
        $this->fieldDefinition = $fieldDefinition;
        if ($class instanceof ObjectbrickDefinition) {
            $config['name'] = 'objectbrick_' . $class->getKey() . '_' . $fieldDefinition->getName();
        } elseif ($class instanceof FieldcollectionDefinition) {
            $config['name'] = 'fieldcollection_' . $class->getKey() . '_' . $fieldDefinition->getName();
        } else {
            $config['name'] = 'object_' . $class->getName() . '_' . $fieldDefinition->getName();
        }
        $this->build($config);
        parent::__construct($config);
    }

    /**
     * @param array $config
     */
    public function build(&$config)
    {
        $fieldHelper = $this->getGraphQlService()->getObjectFieldHelper();
        /** @var Data\AdvancedManyToManyObjectRelation $fieldDefinition */
        $fieldDefinition = $this->fieldDefinition;
        $class = $this->class;

        $className = $fieldDefinition->getAllowedClassId();
        $elementTypeDefinition = ClassTypeDefinitions::get($className);

        $resolver = new ObjectMetadata($fieldDefinition, $class, $fieldHelper);

        $fields = ['element' =>
            [
                'type' => $elementTypeDefinition,
                'resolve' => [$resolver, 'resolveElement']
            ],
            'metadata' => [
                'type' => Type::listOf(new ElementMetadataKeyValuePairType()),
                'resolve' => [$resolver, 'resolveMetadata']

            ]];

        $config['fields'] = $fields;

        return;
    }
}
