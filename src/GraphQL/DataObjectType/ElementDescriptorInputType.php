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

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\ObjectMetadata;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Model\DataObject\ClassDefinition\Data;

/**
 * @internal
 */
final class ElementDescriptorInputType extends InputObjectType
{
    use ServiceTrait;

    /**
     * @var null
     */
    protected $class;

    /** @var Data */
    protected $fieldDefinition;

    /**
     * @param null $class
     * @param array $config
     * @param array $context
     */
    public function __construct(Service $graphQlService,
        ?Data $fieldDefinition = null,
        $class = null,
        $config = ['name' => 'ElementDescriptorInput'],
        $context = [])
    {
        $this->class = $class;
        $this->fieldDefinition = $fieldDefinition;

        $this->setGraphQLService($graphQlService);
        $this->build($config);
        parent::__construct($config);
    }

    /**
     * @param array $config
     */
    public function build(&$config)
    {
        $resolver = new ObjectMetadata($this->fieldDefinition, $this->class, $this->getGraphQlService()->getObjectFieldHelper());

        $config['fields'] = [
            'type' => Type::string(),
            'id' => Type::int(),
            'fullpath' => Type::string(),
            'metadata' => [
                'type' => Type::listOf(new ElementMetadataKeyValuePairInputType()),
                'resolve' => [$resolver, 'resolveMetadata'],
            ],
        ];
        $config['description'] = 'type can be omitted for mutations only allowing one type, e.g. many-to-many-objects.';
    }
}
