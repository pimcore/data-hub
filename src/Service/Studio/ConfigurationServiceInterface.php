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

namespace Pimcore\Bundle\DataHubBundle\Service\Studio;

use Exception;
use Pimcore\Bundle\DataHubBundle\Schema\Configuration;
use Pimcore\Bundle\DataHubBundle\Schema\ConfigurationDetail;
use Pimcore\Bundle\StudioBackendBundle\Exception\Api\ValidationFailedException;

/** @internal  */
interface ConfigurationServiceInterface
{
    /**
     * @return Configuration[]
     */
    public function getConfigurations(): array;

    /**
     * @throws Exception
     */
    public function deleteConfiguration(string $name): void;

    /**
     * @throws Exception
     */
    public function addConfiguration(string $name, string $type, string $path): string;

    /**
     * @throws Exception
     */
    public function getConfiguration(string $name): ConfigurationDetail;

    /**
     * @throws Exception
     */
    public function cloneConfiguration(string $name, string $originalName): string;

    /**
     * @throws Exception
     */
    public function importConfiguration(string $json): array;

    /**
     * @throws Exception
     */
    public function exportConfiguration(string $name): array;

    /**
     * @throws Exception
     * @throws ValidationFailedException
     */
    public function updateConfiguration(string $name, array $configuration, int $clientModificationDate): int;
}
