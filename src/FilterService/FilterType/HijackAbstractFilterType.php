<?php

namespace Pimcore\Bundle\DataHubBundle\FilterService\FilterType;

use Pimcore\Bundle\EcommerceFrameworkBundle\FilterService\FilterType\AbstractFilterType;
use Pimcore\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ProductListInterface;
use Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractFilterDefinitionType;

/**
 * Class HijackAbstractFilterType
 *
 * Allows to access protected methods of AbstractFilterType instances in order
 * to use these data in the Filter Query Type.
 *
 * @package Pimcore\Bundle\DataHubBundle\FilterService\FilterType
 */
class HijackAbstractFilterType extends AbstractFilterType {

    public function getFilterFrontend(
        AbstractFilterDefinitionType $filterDefinition,
        ProductListInterface $productList,
        $currentFilter
    ) {
        return [];
    }

    public function addCondition(
        AbstractFilterDefinitionType $filterDefinition,
        ProductListInterface $productList,
        $currentFilter,
        $params,
        $isPrecondition = FALSE
    ) {
        return [];
    }

    /**
     * Allows to access the procteded getField method of a filter.
     *
     * @param \Pimcore\Bundle\EcommerceFrameworkBundle\FilterService\FilterType\AbstractFilterType $filter
     * @param \Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractFilterDefinitionType $filterDefinition
     * @return string
     */
    public static function getFieldFromFilter(AbstractFilterType $filter, AbstractFilterDefinitionType $filterDefinition) {
        return $filter->getField($filterDefinition);
    }
}
