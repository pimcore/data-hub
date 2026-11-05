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
 * The part of a column the fieldtype token is read from. Structurally satisfied by
 * GridColumnConfiguration from the available-columns payload and by SchemaColumn.
 */
export interface FieldtypeColumn {
  key: string
  type: string
  config?: unknown
}

interface FieldDefinitionConfig {
  fieldDefinition?: { fieldtype?: unknown }
}

const readFieldDefinitionFieldtype = (config: unknown): string | undefined => {
  const fieldtype = (config as FieldDefinitionConfig | undefined)?.fieldDefinition?.fieldtype

  return typeof fieldtype === 'string' && fieldtype.length > 0 ? fieldtype : undefined
}

/**
 * The bare Pimcore field definition type of a column: the fieldtype of its field definition when the
 * available-columns payload carries one, otherwise the segment of the column type after the last dot.
 * Never the column key, which holds the field name (and the user-defined title for advanced columns).
 */
export const deriveColumnFieldtype = (column: FieldtypeColumn): string => {
  const declaredFieldtype = readFieldDefinitionFieldtype(column.config)

  if (declaredFieldtype !== undefined) {
    return declaredFieldtype
  }

  return column.type.slice(column.type.lastIndexOf('.') + 1)
}

/**
 * The field definition type a persisted column carries, or undefined when it carries none. A stored token
 * is only accepted when `isKnownType` knows it, so a configuration written before the token was corrected
 * (it holds the field name) resolves to no type rather than to a guessed one. The editor's predicate is
 * `DynamicTypeFieldDefinitionRegistry.getDynamicType(name, false) !== undefined`.
 */
export const readStoredFieldtype = (
  stored: string | undefined,
  isKnownType: (name: string) => boolean
): string | undefined => {
  if (stored === undefined || stored.length === 0) {
    return undefined
  }

  return isKnownType(stored) ? stored : undefined
}

/**
 * Whether an available column from the payload is the one a persisted column was configured from.
 * Advanced columns are matched on their type because their key holds the user-defined title.
 */
export const matchesAvailableColumn = (
  field: FieldtypeColumn,
  column: FieldtypeColumn,
  advancedColumnType: string
): boolean =>
  field.key === column.key || (column.type === advancedColumnType && field.type === column.type)
