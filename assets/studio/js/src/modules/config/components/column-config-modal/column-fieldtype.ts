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
 * Whether an available column from the payload is the one a persisted column was configured from.
 *
 * An advanced column matches on its type and on nothing else, because its key holds the user-defined
 * title: a column titled `title` must not match an ordinary field keyed `title`. The exclusion runs
 * both ways — an ordinary column matches on key among the non-advanced entries only, so a field keyed
 * like the advanced entry cannot claim it either. Matching an ordinary column by key rather than by
 * type is what keeps a legacy reference such as a Classification Store integer pair resolving.
 */
export const matchesAvailableColumn = (
  field: FieldtypeColumn,
  column: FieldtypeColumn,
  advancedColumnType: string
): boolean => {
  if (column.type === advancedColumnType) {
    return field.type === advancedColumnType
  }

  return field.type !== advancedColumnType && field.key === column.key
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
