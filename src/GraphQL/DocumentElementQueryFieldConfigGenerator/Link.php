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

use Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementType\LinkDataType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;

class Link extends Base
{
    protected $linkDataType;

    public function __construct(Service $graphQlService, LinkDataType $linkDataType)
    {
        $this->linkDataType = $linkDataType;
        parent::__construct($graphQlService);
    }

    /**
     * @return \Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementType\LinkType
     */
    public function getFieldType()
    {
        return \Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementType\LinkType::getInstance($this->linkDataType);
    }
}
