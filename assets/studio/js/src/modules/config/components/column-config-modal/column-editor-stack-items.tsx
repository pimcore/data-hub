/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React from 'react'
import { Tag } from 'antd'
import { IconButton, Space, Spin, type StackListProps } from '@pimcore/studio-ui-bundle/components'
import { isNil } from 'lodash'
import { ColumnEditorItemBody } from './column-editor-item'
import { ColumnLocaleControl } from './column-locale-control'
import { ADVANCED_COLUMN_TYPE, type AdvancedEditorColumn } from './types'

const getColumnLabel = (col: AdvancedEditorColumn): string => {
  if (col.key === '') return ''
  return col.key
}

export interface BuildStackItemsOptions {
  draft: AdvancedEditorColumn[]
  entity: string
  objectId: number | null
  /** Service ID of the DynamicTypePipelineRegistry to use for source fields. */
  sourceFieldsRegistryId: string
  /** Service ID of the DynamicTypePipelineRegistry to use for transformers. */
  transformersRegistryId: string
  onPipelineChange: (id: string, pipeline: Record<string, any>) => void
  onLocaleChange: (id: string, locale: string | null) => void
  onRemove: (id: string) => void
}

/**
 * Builds the StackList item descriptors rendered by the column editor: one entry per draft
 * column, with the pipeline body and the per-column toolbar for advanced columns.
 */
export const buildStackItems = ({
  draft,
  entity,
  objectId,
  sourceFieldsRegistryId,
  transformersRegistryId,
  onPipelineChange,
  onLocaleChange,
  onRemove
}: BuildStackItemsOptions): StackListProps['items'] => draft.map(col => {
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
                onPipelineChange={ onPipelineChange }
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
            onChange={ (locale) => { onLocaleChange(col._id, locale) } }
            value={ col.locale }
          />
        ) }
        <IconButton
          icon={ { value: 'trash' } }
          onClick={ () => { onRemove(col._id) } }
          theme='secondary'
        />
      </Space>
    )
  }
})
