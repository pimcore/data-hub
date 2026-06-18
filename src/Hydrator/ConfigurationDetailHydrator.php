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

use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\Schema\ConfigurationDetail;

/**
 * @internal
 */
final readonly class ConfigurationDetailHydrator implements ConfigurationDetailHydratorInterface
{
    public function hydrate(
        Configuration $configuration,
        array $supportedQueryDataTypes,
        array $supportedMutationDataTypes
    ): ConfigurationDetail {
        return new ConfigurationDetail(
            $configuration->getName(),
            $configuration->getConfiguration(),
            // Resolved permissions of the current user for this configuration (matches the schema
            // example), used by the UI to gate editing/deleting. The raw per-user/role permission
            // sets edited in the permissions tab live in getConfiguration()['permissions'].
            [
                'read' => $configuration->isAllowed('read'),
                'update' => $configuration->isAllowed('update'),
                'delete' => $configuration->isAllowed('delete'),
            ],
            $supportedQueryDataTypes,
            $supportedMutationDataTypes,
            $configuration->getModificationDate()
        );
    }
}
