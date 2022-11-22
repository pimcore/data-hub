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
use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\BlockDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\Helper;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Fieldcollection\Definition;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class BlockEntryType extends ObjectType implements ContainerAwareInterface
{
    /**
     * @var static|null
     */
    protected static $instance;

    use ContainerAwareTrait;

    use ServiceTrait;

    /** @var ClassDefinition */
    protected $class;

    /** @var Data */
    protected $fieldDefinition;

    /**
     * @param Service $graphQlService
     * @param Data $fieldDefinition
     * @param ClassDefinition|null $class
     * @param array $config
     */
    public function __construct(Service $graphQlService, Data $fieldDefinition, $class = null, $config = [])
    {
        $this->class = $class;
        $this->fieldDefinition = $fieldDefinition;
        $this->setGraphQLService($graphQlService);

        $this->build($config);

        parent::__construct($config);
    }

    /**
     * @param string $type
     * @param Service $graphQlService
     * @param Data $fieldDefinition
     * @param ClassDefinition|null $class
     *
     * @return static|null
     */
    public static function getInstance($type, Service $graphQlService, Data $fieldDefinition, $class)
    {
        if (!isset(self::$instance[$type])) {
            $config = [
                'name' => $type
            ];
            self::$instance = new static($graphQlService, $fieldDefinition, $class, $config);
        }

        return self::$instance;
    }

    /**
     * @param array $config
     */
    public function build(&$config)
    {
        if ($this->class instanceof Definition) {
            $name = $this->class->getKey();
        } else {
            $name = $this->class->getName();
        }

        $config['name'] = 'block_'.$name.'_'.$this->fieldDefinition->getName() . '_entry';
        $fields = [];

        $fieldHelper = $this->getGraphQlService()->getObjectFieldHelper();

        Helper::extractDataDefinitions($this->fieldDefinition, $fieldDefinitions);

        foreach ($fieldDefinitions as $fieldDef) {
            if ($fieldDef instanceof ClassDefinition\Data\Localizedfields) {
                $fcLocalizedFieldDefs = $fieldDef->getFieldDefinitions();

                foreach ($fcLocalizedFieldDefs as $localizedFieldDef) {
                    if ($fieldHelper->supportsGraphQL($localizedFieldDef, 'query')) {
                        $fields[$localizedFieldDef->getName()] = $this->prepareField($localizedFieldDef, true);
                    }
                }
            } elseif ($fieldHelper->supportsGraphQL($fieldDef, 'query')) {
                $fields[$fieldDef->getName()] = $this->prepareField($fieldDef);
            }
        }

        $config['fields'] = $fields;
    }

    /**
     * @param Data $fieldDef
     * @param bool $localized
     *
     * @return mixed
     */
    protected function prepareField(Data $fieldDef, bool $localized = false)
    {
        $field = $this->getGraphQlService()->getObjectFieldHelper()->getGraphQlQueryFieldConfig(
            $fieldDef->getName(),
            $fieldDef,
            $this->class,
            $this->container
        );

        $hasResolve = isset($field['resolve']);
        /** @var callable $resolve */
        $resolve = $hasResolve ? $field['resolve'] : null;

        $field['resolve'] = function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) use ($hasResolve, $resolve) {
            if (!$resolveInfo) {
                return null;
            }

            if (!is_array($value)) {
                return null;
            }

            if (!array_key_exists($resolveInfo->fieldName, $value)) {
                return null;
            }

            $value = $value[$resolveInfo->fieldName];

            if (!$value instanceof BlockDescriptor) {
                return null;
            }

            if ($hasResolve) {
                return $resolve($value, $args, $context, $resolveInfo);
            }

            return $this->graphQlService::resolveValue($value, $this->fieldDefinition, $this->fieldDefinition->getName(), $args);
        };

        return $field;
    }
}
