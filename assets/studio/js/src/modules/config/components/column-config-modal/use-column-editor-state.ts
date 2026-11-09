/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { useState, useEffect, useCallback, useMemo } from 'react'
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
  resolvedClassId: string
  availableFields: GridColumnConfiguration[]
  columnGroups: Array<ColumnPickerGroup<GridColumnConfiguration>>
  /** Adds an advanced (pipeline) column, or undefined when the schema offers none. */
  onAddAdvancedColumn?: () => void
  handleAddColumnOfType: (column: GridColumnConfiguration) => void
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
  const { currentData, isLoading } = api.endpoints.dataObjectGetAvailableGridColumns.useQuery({
    classId: resolvedClassId,
    folderId: 1
  })

  const [draft, setDraft] = useState<AdvancedEditorColumn[]>(() =>
    columns.map(advancedFromSchemaColumn)
  )

  useEffect(() => {
    setDraft(columns.map(advancedFromSchemaColumn))
  }, [columns])

  const payloadColumns: GridColumnConfiguration[] | undefined = currentData?.columns
  const availableFields: GridColumnConfiguration[] = useMemo(() => payloadColumns ?? [], [payloadColumns])
  const isHydrated = isDraftHydrated(payloadColumns)

  useEffect(() => {
    if (availableFields.length === 0) return
    setDraft(prev => prev.map(col => {
      const available = availableFields.find(f =>
        f.key === col.key || (col.type === ADVANCED_COLUMN_TYPE && f.type === col.type)
      )
      if (available === undefined) return col
      return {
        ...col,
        localizable: col.localizable ?? available.localizable,
        pipelineConfig: col.pipelineConfig ?? (available.config as Record<string, any> | undefined)
      }
    }))
  }, [availableFields])

  const handleAddColumnOfType = useCallback((column: GridColumnConfiguration): void => {
    setDraft(prev => [...prev, {
      _id: crypto.randomUUID(),
      key: column.key,
      fieldtype: column.key,
      type: column.type,
      pipelineConfig: column.config as Record<string, any> | undefined,
      localizable: column.localizable,
      isNew: true
    }])
  }, [])

  // Only the add-column dropdown is restricted to exportable columns. The full
  // availableFields list is still used above to hydrate already-configured columns,
  // so existing schemas containing non-exportable columns keep rendering unchanged.
  const addColumnFields = useMemo(
    () => exportableOnly ? availableFields.filter(field => field.exportable === true) : availableFields,
    [availableFields, exportableOnly]
  )

  const columnGroups = useAddColumnGroups(addColumnFields)

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
    resolvedClassId,
    availableFields,
    columnGroups,
    onAddAdvancedColumn,
    handleAddColumnOfType,
    handlePipelineChange,
    handleRemove,
    handleApply,
    handleDiscard,
    handleLocaleChange,
    handleReorder,
    getColumns: () => draft.filter(col => col.key !== '').map(advancedToSchemaColumn)
  }
}
