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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreFeatureQueryTypeGenerator;

use Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreFeatureType\DateType;

/**
 * @internal
 */
final class Date extends Base
{
    /**
     * @return DateType
     */
    public function getFieldType()
    {
        return DateType::getInstance();
    }
}
