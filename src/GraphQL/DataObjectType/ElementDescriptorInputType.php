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

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\ObjectMetadata;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Model\DataObject\ClassDefinition\Data;

class ElementDescriptorInputType extends InputObjectType
{
    use ServiceTrait;

    /**
     * @var null
     */
    protected $class;

    /** @var Data */
    protected $fieldDefinition;

    /**
     * @param Service $graphQlService
     * @param Data|null $fieldDefinition
     * @param null $class
     * @param array $config
     * @param array $context
     */
    public function __construct(Service $graphQlService,
                                Data $fieldDefinition = null,
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
                'resolve' => [$resolver, 'resolveMetadata']
            ]
        ];
        $config['description'] = 'type can be omitted for mutations only allowing one type, e.g. many-to-many-objects.';
    }
}
