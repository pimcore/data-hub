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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreFeatureQueryTypeGenerator;

use Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreFeatureType\QuantityValueType;

/**
 * @internal
 */
final class QuantityValue extends Base
{
    /**
     * @return mixed
     *
     * @throws \Exception
     */
    public function getFieldType()
    {
        return QuantityValueType::getInstance($this->getGraphQlService(), 'csFeatureQuantityValue', 'quantity_value', 'quantityvalue');
    }
}
