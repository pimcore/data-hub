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

namespace Pimcore\Bundle\DataHubBundle\Utils\Constants;

/**
 * Adapter types shipped by this bundle. Third party bundles register their own types as plain
 * strings in pimcore_data_hub.supported_types.
 *
 * @internal
 */
enum AdapterType: string
{
    case GraphQl = 'graphql';
}
