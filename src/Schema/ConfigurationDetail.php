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

/**
 * @internal
 */
final readonly class ConfigurationDetail
{
    public function __construct(
        private string $name,
        private array $configuration,
        private array $userPermissions,
        private array $supportedGraphQLQueryDataTypes,
        private array $supportedGraphQLMutationDataTypes,
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

