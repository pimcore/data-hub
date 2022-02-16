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
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Model\DataObject\Data\AbstractQuantityValue;

class QuantityValue
{
    use ServiceTrait;

    /**
     * @param AbstractQuantityValue|null $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     *
     * @return array
     *
     * @throws \Exception
     */
    public function resolveUnit($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        if ($value instanceof AbstractQuantityValue && $unit = $value->getUnit()) {
            return $unit->getObjectVars();
        }

        return [];
    }

    /**
     * @param AbstractQuantityValue|null $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     *
     * @return float|string|null
     *
     * @throws \Exception
     */
    public function resolveValue($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        if ($value instanceof AbstractQuantityValue) {
            return $value->getValue();
        }

        return null;
    }

    /**
     * @param AbstractQuantityValue|null $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     *
     * @return string|null
     *
     * @throws \Exception
     */
    public function resolveToString($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        $returnValue = null;

        if ($value instanceof AbstractQuantityValue) {
            $currentLocale = null;
            $localService = null;
            if (isset($args['language'])) {
                $localService = $this->getGraphQlService()->getLocaleService();
                $currentLocale = $localService->getLocale();

                $localService->setLocale($args['language']);
            }

            $returnValue = $value->__toString();

            if (isset($args['language'])) {
                $localService->setLocale($currentLocale);
            }
        }

        return $returnValue;
    }
}
