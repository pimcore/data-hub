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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectQueryFieldConfigGenerator;

use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectQueryFieldConfigGeneratorInterface;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Bundle\DataHubBundle\GraphQL\TypeDefinitionInterface;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;

class Base implements DataObjectQueryFieldConfigGeneratorInterface, TypeDefinitionInterface
{
    use ServiceTrait;

    public function __construct(Service $graphQlService)
    {
        $this->setGraphQLService($graphQlService);
    }

    /**
     * @param string $attribute
     * @param Data $fieldDefinition
     * @param ClassDefinition|null $class
     * @param object|null $container
     *
     * @return array
     */
    public function getGraphQlFieldConfig($attribute, Data $fieldDefinition, $class = null, $container = null)
    {
        return $this->enrichConfig($fieldDefinition, $class, $attribute, [
            'name' => $fieldDefinition->getName(),
            'type' => $this->getFieldType($fieldDefinition, $class, $container)
        ], $container);
    }

    /**
     * @param Data $fieldDefinition
     * @param ClassDefinition $class
     * @param string $attribute
     * @param array $graphQLConfig
     * @param object|null $container
     *
     * @return array
     */
    public function enrichConfig($fieldDefinition, $class, $attribute, $graphQLConfig, $container = null)
    {
        if ($container instanceof Data\Localizedfields) {
            $graphQLConfig['args'] = $graphQLConfig['args'] ?? [];
            $graphQLConfig['args'] = array_merge($graphQLConfig['args'],
                ['language' => ['type' => Type::string()]
            ]);
        }

        // for non-standard getters we provide a resolve which takes care of the composed x~y~z key. not needed for standard getters.
        if (strpos($attribute, '~') !== false && !isset($graphQLConfig['resolve'])) {
            $resolver = new Helper\Base($this->getGraphQlService(), $attribute, $fieldDefinition, $class);
            $graphQLConfig['resolve'] = [$resolver, 'resolve'];
        }

        return $graphQLConfig;
    }

    /**
     * @param Data $fieldDefinition
     * @param ClassDefinition|null $class
     * @param object|null $container
     *
     * @return Type
     */
    public function getFieldType(Data $fieldDefinition, $class = null, $container = null)
    {
        return Type::string();
    }

    /**
     * @param string $attribute
     * @param Data $fieldDefinition
     * @param ClassDefinition $class
     *
     * @return array|callable(mixed $value, array $args, array $context, \GraphQL\Type\Definition\ResolveInfo $info): mixed
     */
    public function getResolver($attribute, $fieldDefinition, $class)
    {
        $resolver = new Helper\Base($this->getGraphQlService(), $attribute, $fieldDefinition, $class);

        return [$resolver, 'resolve'];
    }
}
