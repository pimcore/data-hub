<?php

declare(strict_types=1);

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreFeatureQueryTypeGenerator;

use Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreFeatureType\ValueType;
use Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType\LinkType;

/**
 * @internal
 */
final class Link extends Base
{
    /**
     * @return ValueType
     */
    public function getFieldType()
    {
        return ValueType::getInstance('csFeatureLink', 'link', LinkType::getInstance());
    }
}
