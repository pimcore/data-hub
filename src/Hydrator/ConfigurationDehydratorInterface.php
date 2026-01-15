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

namespace Pimcore\Bundle\DataHubBundle\Hydrator;

/**
 * @internal
 */
interface ConfigurationDehydratorInterface
{
    /**
     * Dehydrate configuration from client/API format to storage format
     *
     * Transforms:
     * - Query/mutation entities from arrays to associative arrays indexed by id
     * - Special entities from client format (readAllowed, createAllowed, etc.) to storage format (read, create, etc.)
     * - API keys from multiline string to array
     */
    public function dehydrate(array $configuration): array;
}

