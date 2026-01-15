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

namespace Pimcore\Bundle\DataHubBundle\Schema;

use JsonException;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

/**
 * @internal
 */
#[Schema(
    schema: 'BundleDataHubUpdateConfiguration',
    title: 'Bundle Data Hub Update Configuration',
    required: ['data', 'modificationDate'],
    type: 'object'
)]
final readonly class UpdateConfiguration
{
    public function __construct(
        #[Property(
            description: 'Configuration data as JSON string containing general settings, schema (queryEntities, mutationEntities, specialEntities), security, workspaces, and permissions',
            type: 'string',
            example: '{"general":{"active":true,"type":"GraphQL","name":"assets","description":"","group":"GQL"},"schema":{"queryEntities":[],"mutationEntities":[],"specialEntities":[]},"security":{"method":"datahub_apikey","apikey":"your-key","skipPermissionCheck":false,"disableIntrospection":false},"workspaces":{"asset":[],"document":[],"object":[]},"permissions":{"user":[],"role":[]}}'
        )]
        private string $data,
        #[Property(
            description: 'Client-side modification date timestamp for conflict detection',
            type: 'integer',
            example: 1768215191
        )]
        private int $modificationDate,
    ) {
    }

    public function getData(): string
    {
        return $this->data;
    }

    /**
     * @throws JsonException
     */
    public function getConfiguration(): array
    {
        return json_decode(
            $this->data,
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }

    public function getModificationDate(): int
    {
        return $this->modificationDate;
    }
}

