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
  'DataHub/DynamicTypes/Adapter/GraphQL': 'DataHub/DynamicTypes/Adapter/GraphQL',
  'DataHub/DynamicTypes/Operator/GraphQL/QueryRegistry': 'DataHub/DynamicTypes/Operator/GraphQL/QueryRegistry',
  'DataHub/DynamicTypes/Operator/GraphQL/MutationRegistry': 'DataHub/DynamicTypes/Operator/GraphQL/MutationRegistry',
  // Query Operators
  'DataHub/DynamicTypes/Operator/Alias': 'DataHub/DynamicTypes/Operator/Alias',
  'DataHub/DynamicTypes/Operator/Concatenator': 'DataHub/DynamicTypes/Operator/Concatenator',
  'DataHub/DynamicTypes/Operator/DateFormatter': 'DataHub/DynamicTypes/Operator/DateFormatter',
  'DataHub/DynamicTypes/Operator/ElementCounter': 'DataHub/DynamicTypes/Operator/ElementCounter',
  'DataHub/DynamicTypes/Operator/Substring': 'DataHub/DynamicTypes/Operator/Substring',
  'DataHub/DynamicTypes/Operator/Text': 'DataHub/DynamicTypes/Operator/Text',
  'DataHub/DynamicTypes/Operator/Thumbnail': 'DataHub/DynamicTypes/Operator/Thumbnail',
  'DataHub/DynamicTypes/Operator/ThumbnailHtml': 'DataHub/DynamicTypes/Operator/ThumbnailHtml',
  'DataHub/DynamicTypes/Operator/TranslateValue': 'DataHub/DynamicTypes/Operator/TranslateValue',
  'DataHub/DynamicTypes/Operator/Trimmer': 'DataHub/DynamicTypes/Operator/Trimmer',
  // Mutation Operators
  'DataHub/DynamicTypes/Operator/IfEmpty': 'DataHub/DynamicTypes/Operator/IfEmpty',
  'DataHub/DynamicTypes/Operator/LocaleCollector': 'DataHub/DynamicTypes/Operator/LocaleCollector',
  'DataHub/DynamicTypes/Operator/LocaleSwitcher': 'DataHub/DynamicTypes/Operator/LocaleSwitcher'
} as const
