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

use OpenApi\Attributes\Items;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

/**
 * @internal
 */
#[Schema(
    schema: 'BundleDataHubConfigurationDetail',
    title: 'Bundle Data Hub Configuration Detail',
    required: [
        'name',
        'configuration',
        'userPermissions',
        'supportedGraphQLQueryDataTypes',
        'supportedGraphQLMutationDataTypes',
        'modificationDate',
    ],
    type: 'object'
)]
final readonly class ConfigurationDetail
{
    public function __construct(
        #[Property(description: 'Configuration name', type: 'string', example: 'assets')]
        private string $name,
        #[Property(description: 'Configuration data', type: 'object')]
        private array $configuration,
        #[Property(description: 'User permissions', type: 'object', example: ['update' => true, 'delete' => true])]
        private array $userPermissions,
        #[Property(
            description: 'Supported GraphQL query data types',
            type: 'array',
            items: new Items(type: 'string'))
        ]
        private array $supportedGraphQLQueryDataTypes,
        #[Property(
            description: 'Supported GraphQL mutation data types',
            type: 'array',
            items: new Items(type: 'string'))
        ]
        private array $supportedGraphQLMutationDataTypes,
        #[Property(description: 'Modification date timestamp', type: 'integer', example: 1705075200)]
        private int $modificationDate,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function getUserPermissions(): array
    {
        return $this->userPermissions;
    }

    public function getSupportedGraphQLQueryDataTypes(): array
    {
        return $this->supportedGraphQLQueryDataTypes;
    }

    public function getSupportedGraphQLMutationDataTypes(): array
    {
        return $this->supportedGraphQLMutationDataTypes;
    }

    public function getModificationDate(): int
    {
        return $this->modificationDate;
    }
}
