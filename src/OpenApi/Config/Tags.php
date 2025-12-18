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

namespace Pimcore\Bundle\DataHubBundle\OpenApi\Config;

use OpenApi\Attributes\Tag;

#[Tag(
    name: Tags::DataHub->value,
    description: 'bundle_tag_data_hub_description',
)]
/**
 * @internal
 */
enum Tags: string
{
    case DataHub = 'Bundle Data Hub';
}
