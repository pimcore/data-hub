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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Query\Value;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\ElementDescriptor;
use Pimcore\Model\DataObject\Concrete;

class DefaultValue extends AbstractValue
{
    /**
     * @param Concrete|null $element
     *
     * {@inheritdoc}
     */
    public function getLabeledValue($element, ResolveInfo $resolveInfo = null)
    {
        if ($element) {
            if ($this->dataType == 'system') {
                $getter = 'get' . ucfirst($this->attribute);

                return $element->$getter();
            }

            $class = $element->getClass();
            $fieldHelper = $this->getGraphQlService()->getObjectFieldHelper();
            $fieldDefinition = $fieldHelper->getFieldDefinitionFromKey($class, $this->attribute);

            $valueParams = new ElementDescriptor($element);

            $resolveFn = $this->getGraphQlService()->buildDataObjectDataQueryResolver($this->attribute, $fieldDefinition, $class);
            $args = null;
            $context = null;

            $value = $resolveFn($valueParams, $args, $this->context, $resolveInfo);
            if ($value) {
                $value = $this->getGraphQlService()->getElementFromArrayObject($value);

                $result = new \stdClass();
                $result->value = $value;

                return $result;
            }
        }

        return null;
    }
}
