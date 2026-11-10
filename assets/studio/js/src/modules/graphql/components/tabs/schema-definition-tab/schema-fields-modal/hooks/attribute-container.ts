/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

// The backend has no column for these field types; their children are columns of their own (see DataObjectFieldHelper::getFieldDefinitionFromKey()).
const ATTRIBUTE_CONTAINER_FIELD_TYPES = new Set(['localizedfields'])

export const isAttributeContainer = (fieldType: unknown): boolean =>
  typeof fieldType === 'string' && ATTRIBUTE_CONTAINER_FIELD_TYPES.has(fieldType)
