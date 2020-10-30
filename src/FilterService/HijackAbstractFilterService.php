<?php

namespace Pimcore\Bundle\DataHubBundle\FilterService;

use Pimcore\Bundle\EcommerceFrameworkBundle\FilterService\FilterService;

/**
 * Class HijackAbstractFilterService
 *
 * Allows to access protected property of FilterService instances in order
 * to use these data in the Filter Query Type.
 *
 * @package Pimcore\Bundle\DataHubBundle\FilterService
 */
class HijackAbstractFilterService extends FilterService
{

    /**
     * Returns the configured filter types as array
     *
     *
     * @param FilterService $instance
     * @return array
     */
    public static function getFilterTypes(FilterService $instance)
    {
        $types = $instance->filterTypes;
        if (!empty($types)) {
            $filterTypes = [];
            foreach ($types as $key => $filterType) {
                $filterTypes[] = $key;
            }
            return $filterTypes;
        }
        return [];
    }
}
