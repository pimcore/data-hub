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

namespace Pimcore\Bundle\DataHubBundle\Tests\Service\Proposal;

use function array_values;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\Proposal\StoredConfigurationsInterface;

final class InMemoryStoredConfigurations implements StoredConfigurationsInterface
{
    /** @var array<string, Configuration> */
    private array $configurations = [];

    public function put(Configuration $configuration): void
    {
        $this->configurations[(string) $configuration->getName()] = $configuration;
    }

    public function remove(string $name): void
    {
        unset($this->configurations[$name]);
    }

    public function find(string $name): ?Configuration
    {
        return $this->configurations[$name] ?? null;
    }

    public function all(): array
    {
        return array_values($this->configurations);
    }
}
