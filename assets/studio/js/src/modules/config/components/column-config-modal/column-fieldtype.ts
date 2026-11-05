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
  /** The second form of the reference an available column offers, where its key is not the only one. */
  identifier?: string
  config?: unknown
}

/**
 * A column as it comes back from a configuration, carrying the token an earlier save wrote into it.
 */
export interface StoredFieldtypeColumn extends FieldtypeColumn {
  fieldtype?: string
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
 * Whether an available column from the payload is the one a persisted column was configured from. The type
 * has to agree, because the token of a column of another type is written back on Apply. Advanced columns
 * match on their type alone, their key holding the user-defined title; every other column matches its
 * stored reference against both forms the payload offers, so a reference saved as an identifier and one
 * saved before identifiers existed both keep resolving.
 */
export const matchesAvailableColumn = (
  field: FieldtypeColumn,
  column: FieldtypeColumn,
  advancedColumnType: string
): boolean => {
  if (field.type !== column.type) {
    return false
  }

  if (column.type === advancedColumnType) {
    return true
  }

  return field.key === column.key || field.identifier === column.key
}

/**
 * The field definition type a column carries into the editor: that of the available column it was
 * configured from, or — when the payload offers none — its stored token, kept only when it names a known
 * type, so a configuration holding a field name resolves to no type instead of carrying the wrong one on.
 */
export const resolveColumnFieldtype = (
  column: StoredFieldtypeColumn,
  availableColumn: FieldtypeColumn | undefined,
  isKnownType: (name: string) => boolean
): string => {
  if (availableColumn !== undefined) {
    return deriveColumnFieldtype(availableColumn)
  }

  return readStoredFieldtype(column.fieldtype, isKnownType) ?? ''
}
