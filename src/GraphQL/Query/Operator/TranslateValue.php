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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Query\Operator;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Model\Element\ElementInterface;

class TranslateValue extends AbstractOperator
{
    private $prefix;

    /**
     * @param array $config
     * @param array|null $context
     */
    public function __construct(array $config = [], $context = null)
    {
        //TODO use translator factory from grid config
        parent::__construct($config, $context);

        $this->prefix = $config['prefix'];
    }

    /**
     * @param ElementInterface|null $element
     * @param ResolveInfo|null $resolveInfo
     *
     * @return \stdClass|null
     *
     * @throws \Exception
     */
    public function getLabeledValue($element, ResolveInfo $resolveInfo = null)
    {
        $result = new \stdClass();
        $result->label = $this->label;
        $result->value = null;

        $translator = $this->getGraphQlService()->getTranslator();
        $children = $this->getChildren();

        if ($children) {
            $valueResolver = $this->getGraphQlService()->buildValueResolverFromAttributes($children[0]);

            $childResult = $valueResolver->getLabeledValue($element, $resolveInfo);
            if ($childResult) {
                if (is_array($childResult->value)) {
                    $result->value = [];
                    foreach ($childResult->value as $childValue) {
                        $result->value[] = $translator->trans($this->prefix . $childValue, []);
                    }
                } else {
                    $result->value = $translator->trans($this->prefix . $childResult->value, []);
                }

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
