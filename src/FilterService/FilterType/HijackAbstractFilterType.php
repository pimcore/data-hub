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
     * Allows to access the protected getField method of a filter.
     *
     * @param \Pimcore\Bundle\EcommerceFrameworkBundle\FilterService\FilterType\AbstractFilterType $filter
     * @param \Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractFilterDefinitionType $filterDefinition
     * @return string
     */
    public static function getFieldFromFilter(AbstractFilterType $filter, AbstractFilterDefinitionType $filterDefinition) {
        return $filter->getField($filterDefinition);
    }

    /**
     * Returns if the filter accepts multiple values.
     *
     * @FIXME There's currently no sane way to determine if a Filter accepts
     * multiple values. Needs to be changed in the Filter-API.
     *
     * @param \Pimcore\Bundle\EcommerceFrameworkBundle\FilterService\FilterType\AbstractFilterType $filter
     * @param \Pimcore\Bundle\EcommerceFrameworkBundle\Model\AbstractFilterDefinitionType $filterDefinition
     * @return bool
     */
    public static function isMultiValueFilter(AbstractFilterType $filter, AbstractFilterDefinitionType $filterDefinition) {
        // Speculate that there will be an API for this.
        if (method_exists($filter, 'acceptsMultipleValues')) {
            return $filter->acceptsMultipleValues();
        }
        // For now the core filters seem to have the string "Multi" in the class
        // name whenever multiple values are accepted.
        return strpos(get_class($filter), 'Multi') !== FALSE;
    }
}
