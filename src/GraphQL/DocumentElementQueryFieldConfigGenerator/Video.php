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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementQueryFieldConfigGenerator;

use Pimcore\Bundle\DataHubBundle\GraphQL\DocumentType\LinkType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;

class Video extends Base
{

    protected $assetType;

    public function __construct(Service $graphQlService)
    {
        $assetType = $graphQlService->getDataObjectTypeDefinition("asset");
        $this->assetType = $assetType;
        parent::__construct($graphQlService);
    }

    /**
     * @return LinkType
     */
    public function getFieldType()
    {
        return \Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementType\VideoType::getInstance($this->getGraphQlService(), $this->assetType);
    }
}
