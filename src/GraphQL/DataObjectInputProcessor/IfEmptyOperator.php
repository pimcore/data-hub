<?php

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectInputProcessor;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\Exception\ClientSafeException;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData;

/**
 * @internal
 */
final class IfEmptyOperator extends BaseOperator
{
    /**
     * @param array $nodeDef
     */
    public function __construct($nodeDef)
    {
        parent::__construct($nodeDef);
    }

    /**
     * @param Concrete|AbstractData $object
     * @param array $newValue
     * @param array $args
     * @param array $context
     *
     * @return void|null
     *
     * @throws \Exception|\UnexpectedValueException
     */
    public function process($object, $newValue, $args, $context, ResolveInfo $info)
    {
        $class = $object->getClass();

        $nodeDef = $this->nodeDef;
        $nodeDefAttributes = $nodeDef['attributes'];
        $children = $nodeDefAttributes['children'];
        if (!$children) {
            return null;
        }

        if (count($children) !== 1) {
            throw new ClientSafeException('Only one child allowed');
        }

        $firstChild = $children[0];

        if ($firstChild['isOperator']) {
            throw new ClientSafeException('First child should not be an operator');
        }

        $key = $firstChild['attributes']['attribute'];
        $fieldDefinition = $this->getGraphQlService()->getObjectFieldHelper()->getFieldDefinitionFromKey($class, $key);
        if ($fieldDefinition) {
            $valueResolver = $this->getGraphQlService()->buildValueResolverFromAttributes($firstChild);
            $valueFromChild = $valueResolver->getLabeledValue($object, null);

            if (!$valueFromChild || $fieldDefinition->isEmpty($valueFromChild->value)) {
                $parentProcessor = $this->getParentProcessor($this->nodeDef, $class);
                if ($parentProcessor) {
                    call_user_func_array($parentProcessor, [$object, $newValue, $args, $context, $info]);
                }
            }
        }
    }
}
