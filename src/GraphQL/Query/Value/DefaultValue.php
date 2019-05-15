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
 * @category   Pimcore
 * @package    Object
 *
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Query\Value;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Element\ElementInterface;

class DefaultValue extends AbstractValue
{
    /**
     * @param ElementInterface|Concrete $element
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

            $valueParams = [
                'id' => $element->getId(),
            ];

            $resolveFn = $this->getGraphQlService()->buildDataQueryResolver($fieldDefinition, $class);
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
    }
}
