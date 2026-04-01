/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { useCallback, useRef, useState } from 'react'
import { Dropdown, Flex, IconTextButton, Modal, ModalTitle } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { api, type GridColumnConfiguration } from '@pimcore/studio-ui-bundle/api/data-object'
import { useClassDefinitions } from '@pimcore/studio-ui-bundle/modules/data-object'
import { MigrationModal } from '../migration-modal'
import { useAddColumnDropdown } from './use-add-column-dropdown'
import { type ColumnEditorHandle, type SchemaColumn } from './types'

/**
 * Props passed from ColumnConfigModal down to the consumer's renderEditor callback.
 * The consumer must forward `ref` to their editor component so ColumnConfigModal
 * can imperatively call `getColumns()` on confirm.
 */
export interface EditorRenderProps<TColumns = SchemaColumn> {
  ref: React.Ref<ColumnEditorHandle<TColumns>>
  classDefinitionId: string
  columns: TColumns[]
  entity: string
  /** True in the split migration view — the editor should hide its own Apply/Discard toolbar. */
  hideToolbar: boolean
  onApply: (columns: TColumns[]) => void
  onCancel: () => void
}

export interface ColumnConfigModalProps<TColumns = SchemaColumn> {
  entity: string
  /**
   * The numeric Pimcore class definition ID (e.g. "EV", "42").
   * When omitted the ID is resolved from the class name via useClassDefinitions().
   */
  classDefinitionId?: string
  columns: TColumns[]
  /**
   * Legacy ExtJS column configuration blob. When present, the migration flow is shown.
   * When absent (undefined), the normal new-format editor modal is shown.
   */
  columnConfig?: Record<string, unknown>
  open: boolean
  /** Already-translated modal title string. */
  title: string
  onApply: (columns: TColumns[]) => void
  onCancel: () => void
  /**
   * Render prop that returns the adapter-specific column editor element.
   * The consumer MUST forward the `ref` to their editor component (forwardRef).
   *
   * @example
   * renderEditor={(props) => <ColumnEditor {...props} />}
   */
  renderEditor: (props: EditorRenderProps<TColumns>) => React.ReactNode
}

/**
 * Generic ColumnConfigModal for Data Hub adapter bundles.
 *
 * Handles both the new-format path (plain Modal + editor) and the legacy
 * migration path (MigrationModal with split view). The adapter-specific
 * editor is injected via the `renderEditor` render prop.
 */
export const ColumnConfigModal = <TColumns = SchemaColumn>({
  entity,
  classDefinitionId,
  columns,
  columnConfig,
  open,
  title,
  onApply,
  onCancel,
  renderEditor
}: ColumnConfigModalProps<TColumns>): React.JSX.Element => {
  const { t } = useTranslation()
  const { getByName } = useClassDefinitions()

  const resolvedClassId: string = React.useMemo(() => {
    if (classDefinitionId !== undefined) return classDefinitionId
    return getByName(entity)?.id ?? entity
  }, [classDefinitionId, entity, getByName])

  const isLegacy = columnConfig !== undefined

  const [migratedColumns, setMigratedColumns] = useState<TColumns[]>([])
  const columnEditorRef = useRef<ColumnEditorHandle<TColumns>>(null)

  const { data: availableFieldsData } = api.endpoints.dataObjectGetAvailableGridColumns.useQuery(
    { classId: resolvedClassId, folderId: 1 },
    { skip: !isLegacy }
  )
  const availableFields: GridColumnConfiguration[] = availableFieldsData?.columns ?? []

  const handleAddColumn = useCallback((column: GridColumnConfiguration): void => {
    columnEditorRef.current?.addColumn(column)
  }, [])

  const addColumnMenu = useAddColumnDropdown(availableFields, handleAddColumn)

  const handleConfirmMigration = (): void => {
    const cols = columnEditorRef.current?.getColumns() ?? migratedColumns
    onApply(cols)
  }

  const modalTitle = (
    <ModalTitle iconName="settings">
      { title }
    </ModalTitle>
  )

  if (!isLegacy) {
    return (
      <Modal
        footer={ null }
        onCancel={ onCancel }
        open={ open }
        size="XL"
        title={ modalTitle }
      >
        { renderEditor({
          ref: columnEditorRef,
          classDefinitionId: resolvedClassId,
          columns,
          entity,
          hideToolbar: false,
          onApply: (updatedColumns) => {
            onApply(updatedColumns)
            onCancel()
          },
          onCancel
        }) }
      </Modal>
    )
  }

  return (
    <MigrationModal
      legacyConfig={ columnConfig }
      onClose={ onCancel }
      onConfirm={ handleConfirmMigration }
      open={ open }
      renderToolbarLeft={
        <Dropdown menu={ addColumnMenu }>
          <IconTextButton icon={ { value: 'new' } }>
            { t('data-hub.column-config-modal.add-column') }
          </IconTextButton>
        </Dropdown>
      }
      title={ title }
    >
      <Flex
        style={ { flex: 1, minWidth: 0, overflow: 'hidden' } }
        vertical
      >
        { renderEditor({
          ref: columnEditorRef,
          classDefinitionId: resolvedClassId,
          columns: migratedColumns,
          entity,
          hideToolbar: true,
          onApply: (cols) => { setMigratedColumns(cols) },
          onCancel: () => {}
        }) }
      </Flex>
    </MigrationModal>
  )
}
