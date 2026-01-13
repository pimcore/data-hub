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

use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Property;

/**
 * @internal
 */
final class ExplorerUrlJson extends JsonContent
{
    public function __construct()
    {
        parent::__construct(
            required: ['explorerUrl'],
            properties: [
                new Property(
                    property: 'explorerUrl',
                    title: 'explorerUrl',
                    description: 'The GraphQL Explorer URL for the specified configuration',
                    type: 'string',
                    example: '/pimcore-datahub-webservices/explorer/my-config'
                ),
            ],
            type: 'object',
        );
    }
}
