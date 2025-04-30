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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\MultihrefMetadata;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Fieldcollection\Definition;

/**
 * @internal
 */
final class MultihrefMetadataType extends ObjectType
{
    use ServiceTrait;

    /**
     * @var null
     */
    protected $class;

    /** @var Data */
    protected $fieldDefinition;

    /**
     * @param ClassDefinition|Definition|null $class
     * @param array $config
     */
    public function __construct(Service $graphQlService, ?Data $fieldDefinition = null, $class = null, $config = [])
    {
        $this->class = $class;
        $this->setGraphQlService($graphQlService);
        $this->fieldDefinition = $fieldDefinition;
        $name = ($class instanceof Definition) ? $class->getKey() : $class->getName();

        $config['name'] = 'object_'.$name.'_'.$fieldDefinition->getName();
        $this->build($config);
        parent::__construct($config);
    }

    /**
     * @param array $config
     */
    public function build(&$config)
    {
        $fieldDefinition = $this->fieldDefinition;
        $class = $this->class;
        $metadataKeyValuePairType = ElementMetadataKeyValuePairType::getInstance();
        $resolver = new MultihrefMetadata($fieldDefinition, $class, $this->getGraphQlService()->getObjectFieldHelper());
        $fields = ['element' =>
                       [
                           'type' => new HrefType($this->getGraphQlService(), $this->fieldDefinition, $this->class),
                           'resolve' => [$resolver, 'resolveElement'],
                       ],
                   'metadata' => [
                       'type' => Type::listOf($metadataKeyValuePairType),
                       'resolve' => [$resolver, 'resolveMetadata'],
                   ]];

        $config['fields'] = $fields;

        return;
    }
}
