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

use Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementType\ScheduledblockDataType;
use Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementType\ScheduledblockType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;

class Scheduledblock extends Base
{
    /** @var ScheduledblockDataType */
    protected $scheduledblockDataType;

    public function __construct(Service $graphQlService, ScheduledblockDataType $scheduledblockDataType)
    {
        $this->scheduledblockDataType = $scheduledblockDataType;
        parent::__construct($graphQlService);
    }

    /**
     * @return ScheduledblockType
     */
    public function getFieldType()
    {
        return \Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementType\ScheduledblockType::getInstance($this->scheduledblockDataType);
    }
}
