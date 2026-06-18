/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { useCallback, useEffect, useImperativeHandle, useMemo, useState, forwardRef } from 'react'
import { Empty, Tag } from 'antd'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import {
  Button,
  Content,
  ContentLayout,
  Dropdown,
  Flex,
  IconButton,
  IconTextButton,
  Space,
  Spin,
  StackList,
  Toolbar,
  type StackListProps
} from '@pimcore/studio-ui-bundle/components'
import {
  LanguageSelectionContext,
  LanguageSelectionWithProvider
} from '@pimcore/studio-ui-bundle/modules/data-object'
import { useUser } from '@pimcore/studio-ui-bundle/modules/auth'
import { isNil } from 'lodash'
import { ColumnEditorItemBody } from './column-editor-item'
import { ColumnLocaleControl } from './column-locale-control'
import { useColumnEditorState } from './use-column-editor-state'
import {
  ADVANCED_COLUMN_TYPE,
  type ColumnEditorHandle,
  type SchemaColumn,
  type AdvancedEditorColumn
} from './types'

const getColumnLabel = (col: AdvancedEditorColumn): string => {
  if (col.key === '') return ''
  return col.key
}

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
    exportableOnly = false
  }: BaseColumnEditorProps, ref): React.JSX.Element {
    const { t } = useTranslation()
    const user = useUser()

    // Own the language state here. Initialize once from the prop, falling back to
    // the user's first content language. This is the single source of truth —
    // no synchronization effects needed.
    const initialLanguage = language ?? (user.contentLanguages as string[] | undefined)?.[0] ?? 'en'
    const [currentLanguage, setCurrentLanguage] = useState(initialLanguage)
    const [hasLocalizedFields, setHasLocalizedFields] = useState(false)

    // On mount, persist the resolved initial language to the parent so that
    // entities that have never had a language set get one saved immediately.
    useEffect(() => {
      onLanguageChange?.(currentLanguage)
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
      objectId,
      addColumnMenu,
      openElementSelector,
      handleAddColumnOfType,
      handlePipelineChange,
      handleRemove,
      handleApply,
      handleDiscard,
      handleLocaleChange,
      handleReorder,
      getColumns
    } = useColumnEditorState({ entity, classDefinitionId, columns, onApply, onCancel, exportableOnly })

    useImperativeHandle(ref, () => ({
      getColumns,
      addColumn: handleAddColumnOfType
    }), [getColumns, handleAddColumnOfType])

    const stackItems: StackListProps['items'] = draft.map(col => {
      const isAdvanced = col.type === ADVANCED_COLUMN_TYPE
      const label = getColumnLabel(col)

      return {
        id: col._id,
        sortable: true,
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
                    objectId={ objectId }
                    onPipelineChange={ handlePipelineChange }
                    sourceFieldsRegistryId={ sourceFieldsRegistryId }
                    transformersRegistryId={ transformersRegistryId }
                  />
                  )
                : <Spin />
            }
          : {}),
        renderRightToolbar: (
          <Space size='mini'>
            { col.localizable === true && isAdvanced && (
              <ColumnLocaleControl
                onChange={ (locale) => { handleLocaleChange(col._id, locale) } }
                value={ col.locale }
              />
            ) }
            <IconButton
              icon={ { value: 'trash' } }
              onClick={ () => { handleRemove(col._id) } }
              theme='secondary'
            />
          </Space>
        )
      }
    })

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
        <ContentLayout
          renderToolbar={ hideToolbar
            ? undefined
            : (
              <Toolbar
                padding={ { x: 'none', y: 'small' } }
                theme='secondary'
              >
                <Dropdown menu={ addColumnMenu }>
                  <IconTextButton icon={ { value: 'new' } }>
                    { t('data-hub.column-config-modal.add-column') }
                  </IconTextButton>
                </Dropdown>

                <Space size='extra-small'>
                  <Button
                    onClick={ handleDiscard }
                    type='default'
                  >
                    { t('data-hub.column-config-modal.discard') }
                  </Button>

                  <Button
                    onClick={ handleApply }
                    type='primary'
                  >
                    { t('data-hub.column-config-modal.apply') }
                  </Button>
                </Space>
              </Toolbar>
              ) }
          renderTopBar={ (
            <Toolbar
              align='center'
              position='content'
              theme='secondary'
            >
              <Button onClick={ openElementSelector }>
                { t('data-hub.column-config-modal.preview.select-object') }
              </Button>

              <LanguageSelectionWithProvider />
            </Toolbar>
          ) }
        >
          <Content
            padded
            padding={ { x: 'none', y: 'small' } }
            style={ { height: 'calc(80vh - 200px)' } }
          >
            <Space
              direction='vertical'
              style={ { width: '100%' } }
            >
              { draft.length === 0 && (
                <Empty image={ Empty.PRESENTED_IMAGE_SIMPLE } />
              ) }

              { draft.length > 0 && (
                <StackList
                  items={ stackItems }
                  onItemsChange={ (items) => {
                    handleReorder(items.map(item => String(item.id)))
                  } }
                  sortable
                />
              ) }
            </Space>
          </Content>
        </ContentLayout>
      </LanguageSelectionContext.Provider>
    )
  }
)
