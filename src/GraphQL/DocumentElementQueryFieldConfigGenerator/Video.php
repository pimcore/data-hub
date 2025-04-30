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

use Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementType\VideoType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;

/**
 * @internal
 */
final class Video extends Base
{
    /**
     *
     * @throws \Exception
     */
    public function __construct(Service $graphQlService)
    {

        //        $this->assetType = $assetType;
        parent::__construct($graphQlService);
    }

    /**
     * @return VideoType
     */
    public function getFieldType()
    {
        $service = $this->getGraphQlService();
        $assetType = $service->buildAssetType('asset');

        return \Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementType\VideoType::getInstance($this->getGraphQlService(), $assetType);
    }
}
