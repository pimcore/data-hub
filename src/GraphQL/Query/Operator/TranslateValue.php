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
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;

class TranslateValue extends AbstractOperator
{

    private $prefix;

    public function __construct(array $config = [], $context = null)
    {

        //TODO use translator factory from grid config
        parent::__construct($config, $context);

        $this->prefix = $config["prefix"];
    }

    public function getLabeledValue($element, ResolveInfo $resolveInfo = null)
    {

        $result = new \stdClass();
        $result->label = $this->label;
        $result->value = null;

        $translator = \Pimcore::getContainer()->get("pimcore.translator");

        $childs = $this->getChilds();
        if ($childs[0]) {

            $service = \Pimcore::getContainer()->get(Service::class);
            $valueResolver = $service->buildValueResolverFromAttributes($childs[0]);


            $childResult = $valueResolver->getLabeledValue($element, $resolveInfo);
            if ($childResult) {
                $result->value = $translator->trans($this->prefix . $childResult, []);
                return $result;
            }

        }

        return null;
    }

    /**
     * @return mixed
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param mixed $prefix
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }
}
