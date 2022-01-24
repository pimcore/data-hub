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

use Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementType\VideoType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;

class Video extends Base
{
    /**
     * @param Service $graphQlService
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
