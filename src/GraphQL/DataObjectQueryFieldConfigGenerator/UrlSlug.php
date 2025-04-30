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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectQueryFieldConfigGenerator;

use GraphQL\Type\Definition\Type;
use Pimcore\Model\DataObject\ClassDefinition\Data;

/**
 * @internal
 */
final class UrlSlug extends Base
{
    public function getFieldType(Data $fieldDefinition, $class = null, $container = null)
    {
        return Type::listOf($this->getGraphQlService()->getDataObjectTypeDefinition('url_slug'));
    }
}
