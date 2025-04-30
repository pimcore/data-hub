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

use Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementType\EmbedType;

/**
 * @internal
 */
final class Embed extends Base
{
    /**
     * @return EmbedType
     */
    public function getFieldType()
    {
        return EmbedType::getInstance();
    }
}
