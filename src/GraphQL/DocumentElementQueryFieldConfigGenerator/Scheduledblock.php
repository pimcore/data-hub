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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementQueryFieldConfigGenerator;

use Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementType\ScheduledblockDataType;
use Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementType\ScheduledblockType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;

/**
 * @internal
 */
final class Scheduledblock extends Base
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
