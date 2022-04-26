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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectQueryFieldConfigGenerator\Helper;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\ElementDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Bundle\DataHubBundle\WorkspaceHelper;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Data\ObjectMetadata;

class ObjectsMetadata
{
    use ServiceTrait;

    /**
     * @var ClassDefinition\Data
     */
    public $fieldDefinition;

    /**
     * @var ClassDefinition
     */
    public $class;

    /**
     * @var string
     */
    public $attribute;

    /**
     * @param Service $graphQlService
     * @param string $attribute
     * @param ClassDefinition\Data $fieldDefinition
     * @param ClassDefinition $class
     */
    public function __construct(Service $graphQlService, $attribute, $fieldDefinition, $class)
    {
        $this->fieldDefinition = $fieldDefinition;
        $this->class = $class;
        $this->attribute = $attribute;
        $this->setGraphQLService($graphQlService);
    }

    /**
     * @param mixed $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     *
     * @return array|null
     *
     * @throws \Exception
     */
    public function resolve($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        $result = [];
        $relations = \Pimcore\Bundle\DataHubBundle\GraphQL\Service::resolveValue($value, $this->fieldDefinition, $this->attribute, $args);
        if ($relations) {
            /** @var ObjectMetadata $relation */
            foreach ($relations as $relation) {
                $element = $relation->getElement();
                if (!WorkspaceHelper::checkPermission($element, 'read')) {
                    continue;
                }

                $data = [];
                $elementData = new ElementDescriptor($element);
                $this->getGraphQlService()->extractData($elementData, $element, $args, $context, $resolveInfo);

                $elementData['__relation'] = $relation;
                $elementData['__destId'] = $relation->getElement()?->getId();
                $data['element'] = $elementData;
                $data['metadata'] = microtime();

                $result[] = $data;
            }

            return $result;
        }

        return null;
    }
}
