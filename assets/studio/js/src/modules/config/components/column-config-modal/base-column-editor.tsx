/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { ColumnEditorActions } from './column-editor-actions'
import { columnLabel, findAvailableField } from './column-identity'
import React, { useCallback, useEffect, useImperativeHandle, useMemo, useState, forwardRef } from 'react'
import { Empty, Tag } from 'antd'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import {
  Alert,
  Button,
  Content,
  ContentLayout,
  Flex,
  IconTextButton,
  Space,
  Spin,
  StackList,
  Toolbar,
  type StackListProps
} from '@pimcore/studio-ui-bundle/components'
import { useStyles } from './base-column-editor.styles'
import { FieldsToAddPanel } from './fields-to-add-panel'
import { resolvePreviewLanguage } from './preview-language'
import { LanguageSelectionContext } from '@pimcore/studio-ui-bundle/modules/data-object'
import { useUser } from '@pimcore/studio-ui-bundle/modules/auth'
import { isNil } from 'lodash'
import { CollapsedColumnPreview } from './column-preview'
import { ColumnEditorItemBody, ColumnRowActions } from './column-editor-item'
import { useColumnEditorState } from './use-column-editor-state'
import { usePreviewItemPreselect } from './use-preview-item-preselect'
import { SimpleColumnPreview, SimpleColumnPreviewsProvider } from './simple-column-previews'
import { useColumnMarkItems } from './column-marks'
import {
  ADVANCED_COLUMN_TYPE,
  type ColumnEditorHandle,
  type SchemaColumn,
  type AdvancedEditorColumn
} from './types'

export interface BaseColumnEditorProps {
  entity: string
  classDefinitionId?: string
  columns: SchemaColumn[]
  onApply: (columns: SchemaColumn[]) => void
  onCancel: () => void
  /** When true, the Apply/Discard/Add-column toolbar is hidden (used in split migration view). */
  hideToolbar?: boolean
  /** Service ID of the DynamicTypePipelineRegistry to use for source fields. */
  sourceFieldsRegistryId: string
  /** Service ID of the DynamicTypePipelineRegistry to use for transformers. */
  transformersRegistryId: string
  /** The currently selected preview language. When provided, the LanguageSelection is controlled externally. */
  language?: string
  /** Called when the user changes the preview language inside the modal. */
  onLanguageChange?: (language: string) => void
  /** When true, only columns marked as exportable are offered in the add-column dropdown. */
  exportableOnly?: boolean
  /** View only: no apply, add, remove or reorder; a column's settings show disabled. */
  readOnly?: boolean
  /** With `readOnly`: the columns before the change under review, to mark what differs. */
  previousColumns?: SchemaColumn[]
}

export const BaseColumnEditor = forwardRef<ColumnEditorHandle, BaseColumnEditorProps>(
  function BaseColumnEditor ({
    entity,
    classDefinitionId,
    columns,
    onApply,
    onCancel,
    hideToolbar = false,
    sourceFieldsRegistryId,
    transformersRegistryId,
    language,
    onLanguageChange,
    exportableOnly = false,
    readOnly = false,
    previousColumns
  }: BaseColumnEditorProps, ref): React.JSX.Element {
    const { t } = useTranslation()
    const showToolbar = !hideToolbar && !readOnly
    const { styles } = useStyles()
    const user = useUser()
    const [fieldsToAddOpen, setFieldsToAddOpen] = useState(true)

    // Own the language state here, initialized once from the prop, else the user's first content language:
    // the single source of truth, no synchronization effects needed.
    const initialLanguage = resolvePreviewLanguage(language, user.contentLanguages as string[] | undefined)
    const [currentLanguage, setCurrentLanguage] = useState(initialLanguage)
    const [hasLocalizedFields, setHasLocalizedFields] = useState(false)

    // On mount, persist the resolved initial language, so an entity without one gets it saved at once.
    useEffect(() => {
      if (!readOnly) onLanguageChange?.(currentLanguage)
    }, [])

    const handleLanguageChange = useCallback((lang: string) => {
      setCurrentLanguage(lang)
      onLanguageChange?.(lang)
    }, [onLanguageChange])

    // Build a stable context value to pass to the provider.
    const languageContextValue = useMemo(() => ({
      currentLanguage,
      setCurrentLanguage: handleLanguageChange,
      hasLocalizedFields,
      setHasLocalizedFields
    }), [currentLanguage, handleLanguageChange, hasLocalizedFields])

    const {
      draft,
      isLoading,
      isHydrated,
      isError,
      resolvedClassId,
      availableFields,
      columnGroups,
      onAddAdvancedColumn,
      handleAddColumnOfType, handleAddClassificationStoreKey,
      handlePipelineChange,
      handleRemove,
      handleApply,
      handleDiscard,
      handleLocaleChange,
      handleReorder,
      getColumns
    } = useColumnEditorState({ entity, classDefinitionId, columns, onApply, onCancel, exportableOnly })

    const previewEnabled = usePreviewItemPreselect(resolvedClassId)

    useImperativeHandle(ref, () => ({
      getColumns,
      addColumn: handleAddColumnOfType,
      // The readiness probe the modal refuses Confirm Migration on: getColumns carries
      // resolved field definition types only once this class's available columns have loaded.
      isHydrated: () => isHydrated
    }), [getColumns, handleAddColumnOfType, isHydrated])

    const { marks, removedItems } = useColumnMarkItems(columns, previousColumns, readOnly)
    const stackItems: StackListProps['items'] = [...draft.map((col, index) => {
      const isAdvanced = col.type === ADVANCED_COLUMN_TYPE
      const label = columnLabel(col)
      return {
        id: col._id,
        sortable: !readOnly,
        type: isAdvanced ? 'collapse' as const : 'default' as const,
        defaultActive: isAdvanced && col.isNew === true,
        children: isAdvanced
          ? <Tag color='purple'>{ !isNil(col.pipeline?.title) ? String(col.pipeline?.title) : label }</Tag>
          : <Tag>{ label }</Tag>,
        ...(isAdvanced
          ? {
              body: col.pipelineConfig !== undefined
                ? (
                  <ColumnEditorItemBody
                    column={ col }
                    entity={ entity }
                    onPipelineChange={ handlePipelineChange }
                    readOnly={ readOnly }
                    sourceFieldsRegistryId={ sourceFieldsRegistryId }
                    transformersRegistryId={ transformersRegistryId }
                  />
                  )
                : <Spin />,
              collapsedBody: <CollapsedColumnPreview column={ col } />
            }
          : previewEnabled
            ? {
                body: <SimpleColumnPreview
                  availableField={ findAvailableField(col, availableFields) }
                  column={ col }
                      /> 
              }
            : {}),
        renderRightToolbar: (
          <ColumnRowActions
            column={ col }
            mark={ marks[index] }
            onLocaleChange={ handleLocaleChange }
            onRemove={ handleRemove }
            readOnly={ readOnly }
          />
        )
      }
    }), ...removedItems]

    if (isLoading) {
      return (
        <Flex
          align='center'
          justify='center'
          style={ { minHeight: 200 } }
        >
          <Spin />
        </Flex>
      )
    }

    return (
      <LanguageSelectionContext.Provider value={ languageContextValue }>
        <SimpleColumnPreviewsProvider
          availableFields={ availableFields }
          classId={ resolvedClassId }
          columns={ draft }
        >
          <ContentLayout
            renderToolbar={ !showToolbar
              ? undefined
              : (
                <Toolbar
                  padding={ { x: 'none', y: 'small' } }
                  theme='secondary'
                >
                  <Flex gap='mini'>
                    <IconTextButton
                      icon={ { value: 'new' } }
                      onClick={ () => { setFieldsToAddOpen(isOpen => !isOpen) } }
                      type='default'
                    >
                      { t('data-hub.column-config-modal.add-column') }
                    </IconTextButton>

                    { onAddAdvancedColumn !== undefined && (
                    <IconTextButton
                      icon={ { value: 'new' } }
                      onClick={ onAddAdvancedColumn }
                      type='default'
                    >
                      { t('data-hub.column-config-modal.add-advanced-column') }
                    </IconTextButton>
                    ) }
                  </Flex>

                  <ColumnEditorActions
                    isHydrated={ isHydrated }
                    onApply={ handleApply }
                    onDiscard={ handleDiscard }
                  />
                </Toolbar>
                ) }
          >
            <Content
              padded
              padding={ { x: 'none', y: 'small' } }
              style={ { height: 'calc(80vh - 200px)' } }
            >
              { isError && (
              <Alert
                message={ t('data-hub.column-config-modal.fields-to-add.error') }
                type='error'
              />
              ) }
              <div className={ styles.body }>
                { showToolbar && fieldsToAddOpen && (
                <FieldsToAddPanel
                  classId={ resolvedClassId }
                  groups={ columnGroups }
                  isError={ isError }
                  onClose={ () => { setFieldsToAddOpen(false) } }
                  onColumnSelect={ handleAddColumnOfType }
                  onKeySelect={ handleAddClassificationStoreKey }
                />
                ) }

                <div className={ styles.list }>
                  <Space
                    direction='vertical'
                    style={ { width: '100%' } }
                  >
                    { stackItems.length === 0 && (
                    <Empty image={ Empty.PRESENTED_IMAGE_SIMPLE } />
                    ) }

                    { stackItems.length > 0 && (
                    <StackList
                      items={ stackItems }
                      onItemsChange={ (items) => {
                        handleReorder(items.map(item => String(item.id)))
                      } }
                      sortable={ !readOnly }
                    />
                    ) }
                  </Space>
                </div>
              </div>
            </Content>
          </ContentLayout>
        </SimpleColumnPreviewsProvider>
      </LanguageSelectionContext.Provider>
    )
  }
)
