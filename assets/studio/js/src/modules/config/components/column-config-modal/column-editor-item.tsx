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
import { IconButton, Space } from '@pimcore/studio-ui-bundle/components'
import { ADVANCED_COLUMN_TYPE, type AdvancedEditorColumn } from './types'
import { ColumnPipelineForm } from './column-pipeline-form'
import { ColumnLocaleControl } from './column-locale-control'
import { ColumnMark, type ColumnMarkStatus } from './column-marks'

export interface ColumnEditorItemBodyProps {
  column: AdvancedEditorColumn
  entity: string
  onPipelineChange: (id: string, pipeline: Record<string, any>) => void
  /** Service ID of the DynamicTypePipelineRegistry to use for source fields. */
  sourceFieldsRegistryId: string
  /** Service ID of the DynamicTypePipelineRegistry to use for transformers. */
  transformersRegistryId: string
  /** @deprecated Passed to ColumnPipelineForm / ColumnPreview. */
  objectId?: number | null
  /** shows the settings disabled */
  readOnly?: boolean
}

export const ColumnEditorItemBody = ({
  column,
  entity,
  onPipelineChange,
  sourceFieldsRegistryId,
  transformersRegistryId,
  objectId,
  readOnly
}: ColumnEditorItemBodyProps): React.JSX.Element => {
  return (
    <ColumnPipelineForm
      column={ column }
      config={ column.pipelineConfig }
      disabled={ readOnly }
      entity={ entity }
      objectId={ objectId }
      onChange={ (pipeline) => { onPipelineChange(column._id, pipeline) } }
      sourceFieldsRegistryId={ sourceFieldsRegistryId }
      transformersRegistryId={ transformersRegistryId }
      value={ column.pipeline }
    />
  )
}

export interface ColumnRowActionsProps {
  column: AdvancedEditorColumn
  onLocaleChange: (id: string, locale: string | null) => void
  onRemove: (id: string) => void
  /** the locale shows disabled and the column cannot be removed */
  readOnly?: boolean
  /** a review's mark for the column */
  mark?: ColumnMarkStatus
}

export const ColumnRowActions = ({ column, onLocaleChange, onRemove, readOnly, mark }: ColumnRowActionsProps): React.JSX.Element => (
  <Space size='mini'>
    <ColumnMark status={ mark } />
    { column.localizable === true && column.type === ADVANCED_COLUMN_TYPE && (readOnly === true
      ? <Tag>{ column.locale ?? '-' }</Tag>
      : (
        <ColumnLocaleControl
          onChange={ (locale) => { onLocaleChange(column._id, locale) } }
          value={ column.locale }
        />
        )) }
    { readOnly !== true && (
      <IconButton
        icon={ { value: 'trash' } }
        onClick={ () => { onRemove(column._id) } }
        theme='secondary'
      />
    ) }
  </Space>
)
