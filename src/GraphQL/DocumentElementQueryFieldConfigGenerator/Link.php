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

use Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementType\LinkDataType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;

/**
 * @internal
 */
final class Link extends Base
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
