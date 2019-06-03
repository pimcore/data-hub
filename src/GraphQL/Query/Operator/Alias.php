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
    private $start;

    private $length;

    private $ellipses;

    public function __construct(array $config = [], $context = null)
    {
        parent::__construct($config, $context);

        $this->label = $config['cssClass'];
    }

    public function getLabeledValue($element, ResolveInfo $resolveInfo = null)
    {
        $result = new \stdClass();
        $result->label = $this->label;

        $childs = $this->getChilds();

        if (!$childs) {
            return $result;
        } else {
            $c = $childs[0];

            $valueArray = [];
            $valueResolver = $this->getGraphQlService()->buildValueResolverFromAttributes($c);

            $valueFromChild = $valueResolver->getLabeledValue($element, $resolveInfo);;
            if ($valueFromChild) {
                $result->value = $valueFromChild->value;
            }

        }

        return $result;
    }
}
