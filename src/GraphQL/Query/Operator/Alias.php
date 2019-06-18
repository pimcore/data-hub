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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Query\Operator;

use GraphQL\Type\Definition\ResolveInfo;

class Alias extends AbstractOperator
{

    public function getLabeledValue($element, ResolveInfo $resolveInfo = null)
    {
        $result = new \stdClass();
        $result->label = $this->label;

        // Pimcore 5/6 compatibility
        $children = method_exists($this, 'getChildren') ? $this->getChildren() : $this->getChilds();

        if (!$children) {
            return $result;
        } else {
            $c = $children[0];

            $valueResolver = $this->getGraphQlService()->buildValueResolverFromAttributes($c);

            $valueFromChild = $valueResolver->getLabeledValue($element, $resolveInfo);;
            if ($valueFromChild) {
                $result->value = $valueFromChild->value;
            }
        }

        return $result;
    }
}
