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
import { useTranslation, serviceIds, useInjection } from '@pimcore/studio-ui-bundle/app'
import { Grid, GridContentRenderer, Text } from '@pimcore/studio-ui-bundle/components'
import { type DynamicTypeGridCellRegistry } from '@pimcore/studio-ui-bundle/modules/element'
import { useLanguageSelection } from '@pimcore/studio-ui-bundle/modules/data-object'
import { api, type AdvancedColumnConfig } from '@pimcore/studio-ui-bundle/api/data-object'
import { createColumnHelper } from '@tanstack/react-table'
import { type AdvancedEditorColumn } from './types'

export interface ColumnPreviewProps {
  column: AdvancedEditorColumn
  objectId: number | null
  pipelineValue?: Record<string, any>
}

const columnHelper = createColumnHelper()

const PreviewGrid = ({ value }: { value: Array<{ type: string, value: any }> }): React.JSX.Element => {
  const advancedGridCellRegistry = useInjection<DynamicTypeGridCellRegistry>(
    serviceIds['DynamicTypes/AdvancedGridCellRegistry']
  )

  const columns = value.map((item, index) => {
    const isAdvancedCellType = advancedGridCellRegistry.hasDynamicType(item.type)

    return columnHelper.accessor(`${item.type}-${index}`, {
      header: item.type,
      meta: {
        editable: false,
        type: isAdvancedCellType ? item.type : 'dataobject.adapter',
        config: {
          ...(isAdvancedCellType
            ? {}
            : {
                dataObjectType: item.type,
                dataObjectConfig: {}
              })
        }
      }
    })
  })

  const row: Record<string, any> = {}
  value.forEach((item, index) => {
    row[`${item.type}-${index}`] = item.value
  })

  return (
    <GridContentRenderer>
      <Grid
        autoWidth
        columns={ columns }
        data={ [row] }
      />
    </GridContentRenderer>
  )
}

const PreviewResult = ({ column, objectId, pipelineValue }: { column: AdvancedEditorColumn, objectId: number, pipelineValue?: Record<string, any> }): React.JSX.Element => {
  const { t } = useTranslation()
  const { currentLanguage } = useLanguageSelection()

  const pipeline = (pipelineValue !== undefined && Object.keys(pipelineValue).length > 0) ? pipelineValue : column.pipeline

  // Resolve locale: explicit per-column override > global language (only for localizable columns)
  const resolvedLocale = column.localizable === true
    ? (column.locale ?? currentLanguage)
    : undefined

  const { data, error, isLoading } = api.endpoints.dataObjectGetGridPreview.useQuery({
    body: {
      objectId,
      column: {
        type: column.type,
        key: column.key,
        locale: resolvedLocale,
        config: pipeline !== undefined
          ? {
              advancedColumns: pipeline.sourceFields ?? [],
              transformers: pipeline.transformers
            } as unknown as AdvancedColumnConfig
          : undefined
      }
    }
  })

  if (isLoading) {
    return <Text type='secondary'>{ t('data-hub.column-config-modal.preview.loading') }</Text>
  }

  if (error !== undefined) {
    const message = 'error' in (error as object) ? (error as any).error : t('data-hub.column-config-modal.preview.error')
    return <Text type='danger'>{ message }</Text>
  }

  const value = data?.value

  if (value === undefined || value === null || !Array.isArray(value) || value.length === 0) {
    return <Text type='secondary'>{ t('data-hub.column-config-modal.preview.no-data') }</Text>
  }

  return <PreviewGrid value={ value } />
}

export const ColumnPreview = ({ column, objectId, pipelineValue }: ColumnPreviewProps): React.JSX.Element => {
  const { t } = useTranslation()

  return (
    <div style={ { minHeight: 60, display: 'flex', alignItems: 'center' } }>
      { objectId === null
        ? (
          <Text type='secondary'>
            { t('data-hub.column-config-modal.preview.placeholder') }
          </Text>
          )
        : (
          <PreviewResult
            column={ column }
            objectId={ objectId }
            pipelineValue={ pipelineValue }
          />
          ) }
    </div>
  )
}
