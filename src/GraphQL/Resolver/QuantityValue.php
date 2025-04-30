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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Model\DataObject\Data\AbstractQuantityValue;

/**
 * @internal
 */
final class QuantityValue
{
    use ServiceTrait;

    /**
     * @param AbstractQuantityValue|null $value
     * @param array $args
     * @param array $context
     *
     * @return array
     *
     * @throws \Exception
     */
    public function resolveUnit($value = null, $args = [], $context = [], ?ResolveInfo $resolveInfo = null)
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
     *
     * @return float|string|null
     *
     * @throws \Exception
     */
    public function resolveValue($value = null, $args = [], $context = [], ?ResolveInfo $resolveInfo = null)
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
     *
     * @return string|null
     *
     * @throws \Exception
     */
    public function resolveToString($value = null, $args = [], $context = [], ?ResolveInfo $resolveInfo = null)
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
