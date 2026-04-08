/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { useImperativeHandle, forwardRef } from 'react'
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
  LanguageSelectionProvider,
  LanguageSelectionWithProvider
} from '@pimcore/studio-ui-bundle/modules/data-object'
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
    transformersRegistryId
  }: BaseColumnEditorProps, ref): React.JSX.Element {
    const { t } = useTranslation()

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
    } = useColumnEditorState({ entity, classDefinitionId, columns, onApply, onCancel })

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
            { col.localizable === true && (
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
      <LanguageSelectionProvider>
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
      </LanguageSelectionProvider>
    )
  }
)
