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

/**
 * Class PimcoreObjectType
 * @package Pimcore\Bundle\DataHubBundle\GraphQL\Type
 */
class PimcoreObjectType extends ObjectType
{
    use ServiceTrait;

    /**
     * @var string
     */
    protected $className;

    /**
     * @var int
     */
    protected $classId;

    protected static $skipOperators;

    /**
     * @var array
     */
    protected $fields;

    /**
     * PimcoreObjectType constructor.
     * @param Service $graphQlService
     * @param string $className
     * @param string $classId
     * @param array $config
     * @param array $context
     */
    public function __construct(Service $graphQlService, string $className, $classId, $config = [], $context = [])
    {
        $this->className = $className;
        $this->classId = $classId;
        $this->name = $config['name'] = 'object_' . $className;
        $this->setGraphQLService($graphQlService);
        $config['interfaces'] = [Element::getInstance()];
        parent::__construct($config);
    }

    /**
     * @param array $context
     * @throws \Exception
     */
    public function build($context = []): void
    {
        // these are the system fields that are always available, maybe move some of them to FieldHelper so that they
        // are only visible if explicitly configured by the user
        $fields = [
            'id' => [
                'name' => 'id',
                'type' => Type::id(),
            ],
            'classname' => [
                'type' => Type::string(),
            ],
            'parent' => [
                'type' => Type::id(),
            ],
        ];

        if ($context['clientname']) {
            /** @var $configurationItem Configuration */
            $configurationItem = $context['configuration'];

            $columns = $configurationItem->getQueryColumnConfig($this->className)['columns'];

            if ($columns) {
                $class = ClassDefinition::getById($this->classId);
                foreach ($columns as $column) {
                    if (self::$skipOperators && $column['isOperator']) {
                        continue;
                    }

                    /** @var $fieldHelper DataObjectFieldHelper */
                    $fieldHelper = $this->getGraphQlService()->getObjectFieldHelper();
                    $result = $fieldHelper->getQueryFieldConfigFromConfig($column, $class);
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
    public function getFields(): array
    {
        if (null === $this->fields) {
            $fields = $this->config['fields'] ?? [];
            $this->fields = FieldDefinition::defineFieldMap($this, $fields);
        }

        return $this->fields;
    }
}
