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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementQueryFieldConfigGenerator;

use Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementType\RelationType;

class Relation extends Base
{
    /**
     * @return RelationType
     *
     * @throws \Exception
     */
    public function getFieldType()
    {
        return RelationType::getInstance($this->getGraphQlService());
    }
}
