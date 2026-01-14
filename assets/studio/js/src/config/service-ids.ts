/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

/**
 * Service IDs for the Data Hub Bundle
 * Centralized location for all dependency injection service identifiers
 */
export const bundleServiceIds = {
  'DataHub/DynamicTypes/Adapter/Registry': 'DataHub/DynamicTypes/Adapter/Registry',
  'DataHub/DynamicTypes/Adapter/GraphQL': 'DataHub/DynamicTypes/Adapter/GraphQL'
} as const
