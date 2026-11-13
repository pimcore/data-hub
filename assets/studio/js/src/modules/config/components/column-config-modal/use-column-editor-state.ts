/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { useState, useEffect, useCallback, useMemo, useRef } from 'react'
import { api, type GridColumnConfiguration } from '@pimcore/studio-ui-bundle/api/data-object'
import { useClassDefinitions } from '@pimcore/studio-ui-bundle/modules/data-object'
import { type DynamicTypeFieldDefinitionRegistry } from '@pimcore/studio-ui-bundle/modules/field-definitions'
import { serviceIds, useInjection } from '@pimcore/studio-ui-bundle/app'
import { isNil, isUndefined } from 'lodash'
import {
  advancedFromSchemaColumn,
  advancedToSchemaColumn,
  ADVANCED_COLUMN_KEY,
  ADVANCED_COLUMN_TYPE,
  type AdvancedEditorColumn,
  type SchemaColumn
} from './types'
import { type ColumnPickerGroup } from '@pimcore/studio-ui-bundle/components'
import { useAddColumnGroups } from './use-add-column-groups'
import { deriveColumnFieldtype, hydrateDraft, isDraftHydrated } from './column-fieldtype'
import { CLASSIFICATION_STORE_TYPE } from './column-identity'
import { trackConfigError } from '../../../../components/base-detail-view/track-config-error'

interface UseColumnEditorStateOptions {
  entity: string
  classDefinitionId?: string
  columns: SchemaColumn[]
  onApply: (columns: SchemaColumn[]) => void
  onCancel: () => void
  /** When true, only columns marked as exportable are offered in the add-column dropdown. */
  exportableOnly?: boolean
}

interface UseColumnEditorStateResult {
  draft: AdvancedEditorColumn[]
  isLoading: boolean
  /**
   * Whether the available-columns payload for the selected class has loaded, so the draft's field
   * definition types are resolved and it may be submitted. False while the request is in flight or has
   * failed — the toolbar must not offer Apply then. See `isDraftHydrated`.
   */
  isHydrated: boolean
  /** The available-columns request failed, so an empty tree means nothing about the class. */
  isError: boolean
  resolvedClassId: string
  availableFields: GridColumnConfiguration[]
  columnGroups: Array<ColumnPickerGroup<GridColumnConfiguration>>
  /** Adds an advanced (pipeline) column, or undefined when the schema offers none. */
  onAddAdvancedColumn?: () => void
  handleAddColumnOfType: (column: GridColumnConfiguration) => void
  /** Adds one key of a Classification Store field; the whole-store path stays handleAddColumnOfType. */
  handleAddClassificationStoreKey: (field: GridColumnConfiguration, ids: { groupId: number, keyId: number, fieldtype?: string }) => void
  handlePipelineChange: (id: string, pipeline: Record<string, any>) => void
  handleRemove: (id: string) => void
  handleApply: () => void
  handleDiscard: () => void
  handleLocaleChange: (id: string, locale: string | null) => void
  handleReorder: (ids: string[]) => void
  getColumns: () => SchemaColumn[]
}

export const useColumnEditorState = ({
  entity,
  classDefinitionId,
  columns,
  onApply,
  onCancel,
  exportableOnly = false
}: UseColumnEditorStateOptions): UseColumnEditorStateResult => {
  const { getByName } = useClassDefinitions()
  const fieldDefinitionRegistry = useInjection<DynamicTypeFieldDefinitionRegistry>(
    serviceIds['DynamicTypes/FieldDefinitionRegistry']
  )

  const isKnownFieldtype = useCallback(
    (name: string): boolean => !isUndefined(fieldDefinitionRegistry.getDynamicType(name, false)),
    [fieldDefinitionRegistry]
  )

  const resolvedClassId = useMemo(() => {
    if (!isNil(classDefinitionId)) return classDefinitionId
    return getByName(entity)?.id ?? entity
  }, [classDefinitionId, entity, getByName])

  // currentData, not data: during a refetch data still holds the columns of the previous arguments.
  const { currentData, isLoading, isError, error } = api.endpoints.dataObjectGetAvailableGridColumns.useQuery({
    classId: resolvedClassId,
    folderId: 1
  })

  const payloadColumns: GridColumnConfiguration[] | undefined = currentData?.columns
  const payloadRef = useRef(payloadColumns)
  payloadRef.current = payloadColumns

  const [draft, setDraft] = useState<AdvancedEditorColumn[]>(() =>
    hydrateDraft(columns.map(advancedFromSchemaColumn), payloadColumns, ADVANCED_COLUMN_TYPE, isKnownFieldtype)
  )

  // The payload is read through a ref: its arrival is handled by the effect below, and a reload of the
  // configuration must not be undone by it.
  useEffect(() => {
    setDraft(hydrateDraft(columns.map(advancedFromSchemaColumn), payloadRef.current, ADVANCED_COLUMN_TYPE, isKnownFieldtype))
  }, [columns, isKnownFieldtype])

  useEffect(() => {
    if (isError === true) trackConfigError(error)
  }, [isError, error])

  const availableFields: GridColumnConfiguration[] = useMemo(() => payloadColumns ?? [], [payloadColumns])
  const isHydrated = isDraftHydrated(payloadColumns)

  useEffect(() => {
    if (payloadColumns === undefined) return
    setDraft(prev => hydrateDraft(prev, payloadColumns, ADVANCED_COLUMN_TYPE, isKnownFieldtype))
  }, [payloadColumns, isKnownFieldtype])

  const handleAddColumnOfType = useCallback((column: GridColumnConfiguration): void => {
    setDraft(prev => [...prev, {
      _id: crypto.randomUUID(),
      key: column.key,
      fieldtype: deriveColumnFieldtype(column),
      type: column.type,
      pipelineConfig: column.config as Record<string, any> | undefined,
      localizable: column.localizable,
      isNew: true
    }])
  }, [])

  const handleAddClassificationStoreKey = useCallback(
    (field: GridColumnConfiguration, ids: { groupId: number, keyId: number, fieldtype?: string }): void => {
      // D-0012: the bare type of the key itself. The picked value, else the key's own payload entry (matched by
      // ids only: the store field's entry would hand one key the type of the whole store); none known yet is
      // resolved by hydration once the payload carries the key.
      const keyEntry = payloadRef.current?.find(entry => {
        const entryIds = entry.config as Record<string, unknown> | undefined
        return entry.key === field.key && String(entryIds?.groupId) === String(ids.groupId) &&
          String(entryIds?.keyId) === String(ids.keyId)
      })
      setDraft(prev => [...prev, {
        _id: crypto.randomUUID(),
        key: field.key,
        fieldtype: ids.fieldtype ?? (keyEntry !== undefined ? deriveColumnFieldtype(keyEntry) : ''),
        type: CLASSIFICATION_STORE_TYPE,
        config: { groupId: ids.groupId, keyId: ids.keyId },
        localizable: field.localizable,
        isNew: true
      }])
    },
    []
  )

  // Only the add-column dropdown is restricted to exportable columns. The full
  // availableFields list is still used above to hydrate already-configured columns,
  // so existing schemas containing non-exportable columns keep rendering unchanged.
  const addColumnFields = useMemo(
    () => exportableOnly ? availableFields.filter(field => field.exportable === true) : availableFields,
    [availableFields, exportableOnly]
  )

  const columnGroups = useAddColumnGroups(addColumnFields, resolvedClassId !== '')

  // The type first, so an ordinary field which happens to be keyed 'advanced' cannot be offered as the
  // advanced entry. The key is only a fallback for a payload which does not carry the type.
  const advancedColumn = useMemo(
    () => addColumnFields.find(field => field.type === ADVANCED_COLUMN_TYPE) ??
      addColumnFields.find(field => field.key === ADVANCED_COLUMN_KEY),
    [addColumnFields]
  )

  const onAddAdvancedColumn = advancedColumn !== undefined
    ? (): void => { handleAddColumnOfType(advancedColumn) }
    : undefined

  const handlePipelineChange = (id: string, pipeline: Record<string, any>): void => {
    setDraft(prev => prev.map(col =>
      col._id === id ? { ...col, pipeline } : col
    ))
  }

  const handleRemove = (id: string): void => {
    setDraft(prev => prev.filter(col => col._id !== id))
  }

  // Both submission paths are gated on hydration: this one directly, and the modal's Confirm Migration
  // through the handle's isHydrated probe. Without the payload every persisted column still carries the
  // token an earlier save wrote into it, so a save here would write a legacy field name back into
  // `fieldtype`, which is the defect #1709 fixes.
  const handleApply = (): void => {
    if (!isHydrated) {
      return
    }

    onApply(draft.filter(col => col.key !== '').map(advancedToSchemaColumn))
  }

  // Hydrated, not raw: the editor stays mounted after a discard, so with unchanged props and a cached
  // payload no effect reruns and an un-hydrated draft would survive into the next Apply.
  const handleDiscard = (): void => {
    setDraft(
      hydrateDraft(columns.map(advancedFromSchemaColumn), payloadColumns, ADVANCED_COLUMN_TYPE, isKnownFieldtype)
    )
    onCancel()
  }

  const handleLocaleChange = (id: string, locale: string | null): void => {
    setDraft(prev => prev.map(c => c._id === id ? { ...c, locale } : c))
  }

  const handleReorder = (ids: string[]): void => {
    setDraft(prev => {
      return ids
        .map(id => prev.find(col => col._id === id))
        .filter((col): col is AdvancedEditorColumn => col !== undefined)
    })
  }

  return {
    draft,
    isLoading,
    isHydrated,
    isError: isError === true,
    resolvedClassId,
    availableFields,
    columnGroups,
    onAddAdvancedColumn,
    handleAddColumnOfType,
    handleAddClassificationStoreKey,
    handlePipelineChange,
    handleRemove,
    handleApply,
    handleDiscard,
    handleLocaleChange,
    handleReorder,
    getColumns: () => draft.filter(col => col.key !== '').map(advancedToSchemaColumn)
  }
}
