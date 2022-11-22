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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreFeatureQueryTypeGenerator;

use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreFeatureType\QuantityValueType;

class InputQuantityValue extends Base
{
    /**
     * @return mixed
     *
     * @throws \Exception
     */
    public function getFieldType()
    {
        return QuantityValueType::getInstance($this->getGraphQlService(), 'csFeatureInputQuantityValue', 'input_quantity_value', 'inputquantityvalue');
    }
}
