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
import { isNil } from 'lodash'
import {
  advancedFromSchemaColumn,
  advancedToSchemaColumn,
  ADVANCED_COLUMN_TYPE,
  type AdvancedEditorColumn,
  type SchemaColumn
} from './types'
import { useAddColumnDropdown, type AddColumnDropdownMenu } from './use-add-column-dropdown'

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
}

interface UseColumnEditorStateResult {
  draft: AdvancedEditorColumn[]
  isLoading: boolean
  objectId: number | null
  availableFields: GridColumnConfiguration[]
  addColumnMenu: AddColumnDropdownMenu
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
  onCancel
}: UseColumnEditorStateOptions): UseColumnEditorStateResult => {
  const { getByName } = useClassDefinitions()

  const resolvedClassId = useMemo(() => {
    if (!isNil(classDefinitionId)) return classDefinitionId
    return getByName(entity)?.id ?? entity
  }, [classDefinitionId, entity, getByName])

  const { data, isLoading } = api.endpoints.dataObjectGetAvailableGridColumns.useQuery({
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

  const availableFields: GridColumnConfiguration[] = data?.columns ?? []

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
      localizable: column.localizable
    }])
  }, [])

  const addColumnMenu = useAddColumnDropdown(availableFields, handleAddColumnOfType)

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

  const handleDiscard = (): void => {
    setDraft(columns.map(advancedFromSchemaColumn))
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
    addColumnMenu,
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
