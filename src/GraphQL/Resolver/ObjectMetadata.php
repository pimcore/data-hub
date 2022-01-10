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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper\DataObjectFieldHelper;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Data;

class ObjectMetadata
{
    use ServiceTrait;

    /** @var ClassDefinition\Data|null */
    protected $fieldDefinition;

    /** @var ClassDefinition|null */
    protected $class;

    /** @var DataObjectFieldHelper|null */
    protected $fieldHelper;

    /**
     * @param ClassDefinition\Data|null $fieldDefinition
     * @param ClassDefinition|null $class
     * @param DataObjectFieldHelper|null $fieldHelper
     */
    public function __construct($fieldDefinition = null, $class = null, $fieldHelper = null)
    {
        $this->fieldDefinition = $fieldDefinition;
        $this->class = $class;
        $this->fieldHelper = $fieldHelper;
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
    public function resolveElement($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        $element = null;

        if (!$value['element']) {
            return null;
        }

        if ($value['element']['__elementType'] == 'object') {
            $element = AbstractObject::getById($value['element']['__destId']);
        } else {
            if ($value['element']['__elementType'] == 'asset') {
                $element = Asset::getById($value['element']['__destId']);
            }
        }

        if (!$element) {
            return null;
        }

        $data = $value['element'];
        $this->fieldHelper->extractData($data, $element, $args, $context, $resolveInfo);

        return $data;
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
    public function resolveMetadata($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        if ($value && $value['element']) {
            /** @var Data\ObjectMetadata $relation */
            $relation = $value['element']['__relation'];
            $meta = $relation->getData();
            $result = [];
            if ($meta) {
                foreach ($meta as $metaItemKey => $metaItemValue) {
                    $result[] = [
                        'name' => $metaItemKey,
                        'value' => $metaItemValue,
                    ];
                }
            }

            return $result;
        }

        return null;
    }
}
