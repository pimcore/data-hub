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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\QueryFieldConfigGenerator;

use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\QueryFieldConfigGeneratorInterface;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Bundle\DataHubBundle\GraphQL\TypeDefinitionInterface;
use Pimcore\Model\DataObject\ClassDefinition\Data;

class Base implements QueryFieldConfigGeneratorInterface, TypeDefinitionInterface
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
     * @param $attribute
     * @param Data $fieldDefinition
     * @param null $class
     * @param null $container
     *
     * @return mixed
     */
    public function getGraphQlFieldConfig($attribute, Data $fieldDefinition, $class = null, $container = null)
    {
        return $this->enrichConfig($fieldDefinition, $class, $attribute, [
            'name' => $fieldDefinition->getName(),
            'type' => $this->getFieldType($fieldDefinition, $class, $container)
        ], $container);
    }

    /**
     * @param $fieldDefinition
     * @param $class
     * @param $attribute
     * @param $grapQLConfig
     * @param $container
     *
     * @return mixed
     */
    public function enrichConfig($fieldDefinition, $class, $attribute, $grapQLConfig, $container = null)
    {
        if ($container instanceof Data\Localizedfields) {
            $grapQLConfig['args'] = $grapQLConfig['args'] ? $grapQLConfig['args'] : [];
            $grapQLConfig['args'] = array_merge($grapQLConfig['args'],
                ['language' => ['type' => Type::string()]
            ]);
        }

        // for non-standard getters we provide a resolve which takes care of the composed x~y~z key. not needed for standard getters.
        if (strpos($attribute, "~") !== FALSE && !$grapQLConfig['resolve']) {
            $resolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\QueryFieldConfigGenerator\Helper\Base($this->getGraphQlService(), $attribute, $fieldDefinition, $class);
            $grapQLConfig['resolve'] = [$resolver, "resolve"];
        }

        return $grapQLConfig;
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
     * @param $attribute
     * @param Data $fieldDefinition
     * @param $class
     *
     * @return \Closure
     */
    public function getResolver($attribute, $fieldDefinition, $class)
    {
        $resolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\QueryFieldConfigGenerator\Helper\Base($this->getGraphQlService(), $attribute, $fieldDefinition, $class);
        return [$resolver, "resolve"];
    }

}
