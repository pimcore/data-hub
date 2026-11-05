/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { useState, useEffect, useRef, useCallback, useMemo } from 'react'
import { useElementSelector, SelectionType } from '@pimcore/studio-ui-bundle/modules/element'
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
import { deriveColumnFieldtype, hydrateDraft } from './column-fieldtype'

const SYSTEM_COLUMNS = [
  { key: 'id', type: 'system.id', group: ['system'] as string[], config: [] as never[] },
  { key: 'fullpath', type: 'system.string', group: ['system'] as string[], config: [] as never[] }
]

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
  objectId: number | null
  availableFields: GridColumnConfiguration[]
  columnGroups: Array<ColumnPickerGroup<GridColumnConfiguration>>
  /** Adds an advanced (pipeline) column, or undefined when the schema offers none. */
  onAddAdvancedColumn?: () => void
  openElementSelector: () => void
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

  const [objectId, setObjectId] = useState<number | null>(null)
  const hasManualSelection = useRef(false)

  const { data: gridData } = api.endpoints.dataObjectGetGrid.useQuery(
    { classId: resolvedClassId, body: { folderId: 1, columns: SYSTEM_COLUMNS, filters: { includeDescendants: true, page: 1, pageSize: 1 } } },
    { skip: resolvedClassId === undefined }
  )

  useEffect(() => {
    if (hasManualSelection.current) return
    const firstItem = gridData?.items?.[0]
    if (firstItem?.id !== undefined) {
      setObjectId(firstItem.id as number)
    }
  }, [gridData?.items])

  const { open: openElementSelector } = useElementSelector({
    selectionType: SelectionType.Single,
    areas: { object: true, asset: false, document: false },
    config: {
      objects: {
        allowedTypes: ['object'],
        ...(entity !== undefined ? { allowedClasses: [entity] } : {})
      }
    },
    onFinish: (event) => {
      const item = event?.items?.[0]
      if (item !== undefined) {
        hasManualSelection.current = true
        setObjectId(item.data.id)
      }
    }
  })

  const payloadColumns: GridColumnConfiguration[] | undefined = currentData?.columns
  const availableFields: GridColumnConfiguration[] = useMemo(() => payloadColumns ?? [], [payloadColumns])

  // Runs on the draft the reset effect above has just rebuilt from `columns`, and again when the payload
  // arrives. Without the payload hydrateDraft hands the draft back untouched, stored token and all.
  useEffect(() => {
    setDraft(prev => hydrateDraft(prev, payloadColumns, ADVANCED_COLUMN_TYPE, isKnownFieldtype))
  }, [payloadColumns, columns, isKnownFieldtype])

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

  const handleApply = (): void => {
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
    objectId,
    availableFields,
    columnGroups,
    onAddAdvancedColumn,
    openElementSelector,
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
