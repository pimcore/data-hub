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
import { type AdvancedEditorColumn } from './types'
import { ColumnPipelineForm } from './column-pipeline-form'

export interface ColumnEditorItemBodyProps {
  column: AdvancedEditorColumn
  entity: string
  onPipelineChange: (id: string, pipeline: Record<string, any>) => void
  /** Service ID of the DynamicTypePipelineRegistry to use for source fields. */
  sourceFieldsRegistryId: string
  /** Service ID of the DynamicTypePipelineRegistry to use for transformers. */
  transformersRegistryId: string
}

export const ColumnEditorItemBody = ({
  column,
  entity,
  onPipelineChange,
  sourceFieldsRegistryId,
  transformersRegistryId
}: ColumnEditorItemBodyProps): React.JSX.Element => {
  return (
    <ColumnPipelineForm
      column={ column }
      config={ column.pipelineConfig }
      entity={ entity }
      onChange={ (pipeline) => { onPipelineChange(column._id, pipeline) } }
      sourceFieldsRegistryId={ sourceFieldsRegistryId }
      transformersRegistryId={ transformersRegistryId }
      value={ column.pipeline }
    />
  )
}
