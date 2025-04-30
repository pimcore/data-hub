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

use Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementType\AreablockDataType;
use Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementType\AreablockType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;

/**
 * @internal
 */
final class Areablock extends Base
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
