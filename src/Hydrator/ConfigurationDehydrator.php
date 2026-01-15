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
 *
 * Dehydrates configuration data from client/API format to storage format.
 * This is the inverse operation of hydration - converting from DTOs/schemas
 * back to the internal domain model format.
 */
final readonly class ConfigurationDehydrator implements ConfigurationDehydratorInterface
{
    public function dehydrate(array $configuration): array
    {
        $configuration = $this->dehydrateSchemaEntities($configuration);
        $configuration = $this->dehydrateSpecialEntities($configuration);

        return $this->dehydrateApiKeys($configuration);
    }

    private function dehydrateSchemaEntities(array $configuration): array
    {
        $keys = ['queryEntities', 'mutationEntities'];
        foreach ($keys as $key) {
            $transformedEntities = [];
            if (isset($configuration['schema'][$key]) && is_array($configuration['schema'][$key])) {
                foreach ($configuration['schema'][$key] as $entity) {
                    if (isset($entity['id'])) {
                        $transformedEntities[$entity['id']] = $entity;
                    }
                }
            }
            $configuration['schema'][$key] = $transformedEntities;
        }

        return $configuration;
    }

    private function dehydrateSpecialEntities(array $configuration): array
    {
        if (!isset($configuration['schema']['specialEntities']) || !is_array($configuration['schema']['specialEntities'])) {
            return $configuration;
        }

        $transformedEntities = [];

        foreach ($configuration['schema']['specialEntities'] as $entity) {
            if (isset($entity['name'])) {
                $transformedEntities[$entity['name']] = [
                    'read' => $entity['readAllowed'] ?? false,
                    'create' => $entity['createAllowed'] ?? false,
                    'update' => $entity['updateAllowed'] ?? false,
                    'delete' => $entity['deleteAllowed'] ?? false,
                ];
            }
        }

        $configuration['schema']['specialEntities'] = $transformedEntities;

        return $configuration;
    }

    private function dehydrateApiKeys(array $configuration): array
    {
        if (isset($configuration['security']['apikey']) && is_string($configuration['security']['apikey'])) {
            $configuration['security']['apikey'] = explode(
                "\n",
                trim($configuration['security']['apikey'], "\n")
            );
        }

        return $configuration;
    }
}
