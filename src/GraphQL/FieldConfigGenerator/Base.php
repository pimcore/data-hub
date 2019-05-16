<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\FieldConfigGenerator;

use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\FieldConfigGeneratorInterface;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Bundle\DataHubBundle\GraphQL\TypeDefinitionInterface;
use Pimcore\Model\DataObject\ClassDefinition\Data;

class Base implements FieldConfigGeneratorInterface, TypeDefinitionInterface
{

    use ServiceTrait;

    /**
     * @var Service
     */
    protected $graphQlService;

    /**
     * Base constructor.
     * @param Service $graphQlService
     */
    public function __construct(Service $graphQlService)
    {
        $this->setGraphQLService($graphQlService);
    }

    /**
     * @param Data $fieldDefinition
     * @param null $class
     * @param null $container
     *
     * @return mixed
     */
    public function getGraphQlFieldConfig(Data $fieldDefinition, $class = null, $container = null)
    {
        return $this->enrichConfig([
            'name' => $fieldDefinition->getName(),
            'type' => $this->getFieldType($fieldDefinition, $class, $container)
        ], $container);
    }

    /**
     * @param $config
     * @param $container
     *
     * @return mixed
     */
    public function enrichConfig($config, $container = null)
    {
        if ($container instanceof Data\Localizedfields) {
            $config['args'] = $config['args'] ? $config['args'] : [];
            $config['args'] = array_merge($config['args'],
                ['language' => ['type' => Type::string()]
            ]);
        }

        return $config;
    }

    /**
     * @param Data $fieldDefinition
     * @param null $class
     * @param null $container
     *
     * @return \GraphQL\Type\Definition\ListOfType|mixed
     */
    public function getFieldType(Data $fieldDefinition, $class = null, $container = null)
    {
        return Type::string();
    }

    /**
     * @param Data $fieldDefinition
     * @param $class
     *
     * @return \Closure
     */
    public function getResolver($fieldDefinition, $class)
    {
        $resolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\FieldConfigGenerator\Helper\Base($this->getGraphQlService(), $fieldDefinition, $class);
        return [$resolver, "resolve"];
    }

}
