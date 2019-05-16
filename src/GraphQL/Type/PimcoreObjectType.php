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
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Type;

use GraphQL\Error\InvariantViolation;
use GraphQL\Type\Definition\FieldDefinition;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper\DataObjectFieldHelper;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Bundle\DataHubBundle\GraphQL\TypeInterface\Element;
use Pimcore\Model\DataObject\ClassDefinition;

class PimcoreObjectType extends ObjectType
{
    use ServiceTrait;

    /** @var ClassDefinition */
    protected $class;

    protected static $skipOperators;

    protected $fields;


    /**
     * PimcoreObjectType constructor.
     * @param Service $service
     * @param $class
     * @param array $config
     * @param array $context
     */
    public function __construct(Service $graphQlService, $class, $config = [], $context = [])
    {
        $this->class = $class;
        $this->name = $config['name'] = 'object_' . $class->getName();
        $this->setGraphQLService($graphQlService);
        $config['interfaces'] = [Element::getInstance()];
        parent::__construct($config);
    }

    /**
     * @param $config
     * @param array $context
     */
    public function build($context = [])
    {
        // these are the system fields that are always available, maybe move some of them to FieldHelper so that they
        // are only visible if explicitly configured by the user
        $fields = ['id' =>
            ['name' => 'id',
                'type' => Type::id(),
            ],
//            "fullpath" => Type::string(),
//            "key" => Type::string(),
            'classname' => [
                'type' => Type::string(),
            ],

        ];

        if ($context['clientname']) {

            /** @var $configurationItem Configuration */
            $configurationItem = $context['configuration'];

            $columns = $configurationItem->getColumnConfig($this->class->getName())['columns'];

            if ($columns) {
                foreach ($columns as $column) {
                    if ($column['isOperator'] && self::$skipOperators) {
                        continue;
                    }

                    /** @var $fieldHelper DataObjectFieldHelper */
                    $fieldHelper = $this->getGraphQlService()->getObjectFieldHelper();
                    $result = $fieldHelper->getFieldConfigFromConfig($column, $this->class, ['isRoot' => true]);
                    if ($result) {
                        $fields[$result['key']] = $result['config'];
                    }
                }
            }
        }

        $this->fields = null;
        $this->config['fields'] = $fields;
    }

    /**
     * @return ClassDefinition
     */
    public function getClass(): ClassDefinition
    {
        return $this->class;
    }

    /**
     * @param ClassDefinition $class
     */
    public function setClass(ClassDefinition $class): void
    {
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function getFieldname(): string
    {
        return $this->fieldname;
    }

    /**
     * @param string $fieldname
     */
    public function setFieldname(string $fieldname): void
    {
        $this->fieldname = $fieldname;
    }

    /**
     * @return mixed
     */
    public static function getSkipOperators()
    {
        return self::$skipOperators;
    }

    /**
     * @param mixed $skipOperators
     */
    public static function setSkipOperators($skipOperators): void
    {
        self::$skipOperators = $skipOperators;
    }

    /**
     * @return FieldDefinition[]
     *
     * @throws InvariantViolation
     */
    public function getFields()
    {
        if (null === $this->fields) {
            $fields = isset($this->config['fields']) ? $this->config['fields'] : [];
            $this->fields = FieldDefinition::defineFieldMap($this, $fields);
        }

        return $this->fields;
    }
}
