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
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\QueryFieldConfigGenerator;

use Pimcore\Bundle\DataHubBundle\GraphQL\Type\QuantityValueType;
use Pimcore\Model\DataObject\ClassDefinition\Data;

class QuantityValue extends Base
{
    /**
     * @inheritdoc
     *
     */
    public function getFieldType(Data $fieldDefinition, $class = null, $container = null)
    {
        return QuantityValueType::getInstance($this->getGraphQlService(), $fieldDefinition, null, $class, $container);
    }
}
