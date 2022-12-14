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

use Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementType\AreablockDataType;
use Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementType\AreablockType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;

class Areablock extends Base
{
    protected $areablockDataType;

    public function __construct(Service $graphQlService, AreablockDataType $areablockDataType)
    {
        $this->areablockDataType = $areablockDataType;
        parent::__construct($graphQlService);
    }

    /**
     * @return AreablockType
     */
    public function getFieldType()
    {
        return \Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementType\AreablockType::getInstance($this->areablockDataType);
    }
}
