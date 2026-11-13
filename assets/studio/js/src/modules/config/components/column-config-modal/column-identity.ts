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
 * Column identity for the preview request and response. Mirrors the rules of studio's listing column mapper
 * (use-column-mapper.tsx:43-57 in studio-ui-bundle, which the SDK does not export): a classification-store column
 * is told apart by key, keyId, groupId and locale; every other column by key and locale.
 */

export const CLASSIFICATION_STORE_TYPE = 'dataobject.classificationstore'

interface IdentityColumn {
  key: string
  type: string
  locale?: string | null
  config?: Record<string, any>
  pipelineConfig?: Record<string, any>
}

const idPart = (value: unknown): string => value === undefined || value === null ? '' : String(value)

const identityOf = (key: string, locale: string | null | undefined, keyId: unknown, groupId: unknown): string =>
  `${key}|${idPart(keyId)}|${idPart(groupId)}|${locale ?? ''}`

export const isClassificationStore = (type: string | undefined): boolean => type === CLASSIFICATION_STORE_TYPE

/** keyId and groupId of a column: its own config, else the pipeline config of the available field */
export const columnIds = (column: Pick<IdentityColumn, 'config' | 'pipelineConfig'>): { keyId?: unknown, groupId?: unknown } => {
  const source = column.config?.keyId !== undefined || column.config?.groupId !== undefined
    ? column.config
    : column.pipelineConfig
  return { keyId: source?.keyId, groupId: source?.groupId }
}

/** Identity of a draft column; `locale` is the resolved request locale */
export const columnIdentity = (column: IdentityColumn, locale: string | null | undefined): string => {
  if (!isClassificationStore(column.type)) return identityOf(column.key, locale, undefined, undefined)
  const { keyId, groupId } = columnIds(column)
  return identityOf(column.key, locale, keyId, groupId)
}

/** Identity of a response entry: the key's prefix before the first dot, `additionalAttributes` for the ids */
export const responseIdentity = (
  entry: { key: unknown, type?: unknown, locale?: string | null, additionalAttributes?: Record<string, any> },
  isStore: boolean
): string => {
  const key = String(entry.key)
  if (!isStore) return identityOf(key, entry.locale, undefined, undefined)
  const prefix = key.split('.')[0]
  return identityOf(prefix, entry.locale, entry.additionalAttributes?.keyId, entry.additionalAttributes?.groupId)
}

/** The available field a column belongs to: by ids for classification-store columns, else by key */
export const findAvailableField = <T extends { key: string, config?: any }>(
  column: IdentityColumn,
  availableFields: T[]
): T | undefined => {
  const sameKey = availableFields.filter(field => field.key === column.key)
  if (!isClassificationStore(column.type)) return sameKey[0]
  const { keyId, groupId } = columnIds(column)
  if (keyId === undefined && groupId === undefined) return sameKey[0]
  return sameKey.find(field => idPart(field.config?.keyId) === idPart(keyId) && idPart(field.config?.groupId) === idPart(groupId))
}

/** Store used when a store field never had one configured (the default store of a Pimcore installation) */
const DEFAULT_STORE_ID = 1

/** The id of the Classification Store a store field belongs to; a missing or non-numeric id is the default store */
export const classificationStoreId = (config: unknown): number => {
  const storeId = (config as { fieldDefinition?: { storeId?: unknown } } | null | undefined)?.fieldDefinition?.storeId
  return typeof storeId === 'number' ? storeId : DEFAULT_STORE_ID
}

/** Row label of a column: `key-groupId-keyId` for a per-key classification-store column, else the key */
export const columnLabel = (column: IdentityColumn): string => {
  if (column.key === '') return ''
  if (!isClassificationStore(column.type)) return column.key
  // The persisted config only: the pipeline config is borrowed from an available entry and says nothing
  // about which key this column stands for, so a whole-store column must not read as one of its keys.
  const keyId = column.config?.keyId
  const groupId = column.config?.groupId
  return keyId === undefined || groupId === undefined ? column.key : `${column.key}-${idPart(groupId)}-${idPart(keyId)}`
}

/**
 * The available entry a persisted classification-store column was configured from, reading the ids from its own
 * config only: the entry with the same ids, else (a whole-store column holds none) the entry without ids, else
 * the first entry of that field name.
 */
export const findStoredStoreField = <T extends { key: string, config?: any }>(
  column: Pick<IdentityColumn, 'key' | 'config'>,
  candidates: T[]
): T | undefined => {
  const sameKey = candidates.filter(field => field.key === column.key)
  const keyId = column.config?.keyId
  const groupId = column.config?.groupId
  if (keyId === undefined && groupId === undefined) {
    return sameKey.find(field => field.config?.keyId === undefined && field.config?.groupId === undefined) ?? sameKey[0]
  }
  return sameKey.find(field => idPart(field.config?.keyId) === idPart(keyId) && idPart(field.config?.groupId) === idPart(groupId)) ??
    sameKey.find(field => field.config?.keyId === undefined && field.config?.groupId === undefined)
}
