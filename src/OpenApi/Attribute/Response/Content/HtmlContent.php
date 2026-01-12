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

namespace Pimcore\Bundle\DataHubBundle\OpenApi\Attribute\Response\Content;

use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\Schema;

/**
 * @internal
 */
final class HtmlContent extends MediaType
{
    public function __construct(string $description = 'HTML content')
    {
        parent::__construct(
            mediaType: 'text/html',
            schema: new Schema(
                description: $description,
                type: 'string',
                example: '<!DOCTYPE html><html lang="en"><body>...</body></html>'
            )
        );
    }
}
