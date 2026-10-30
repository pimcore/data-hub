/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { useEffect, useRef, useState } from 'react'
import { useTranslation, serviceIds, useInjection } from '@pimcore/studio-ui-bundle/app'
import {
  ColumnPreview as PreviewModeColumnPreview,
  Grid,
  GridContentRenderer,
  Text,
  usePreviewMode
} from '@pimcore/studio-ui-bundle/components'
import { type DynamicTypeGridCellRegistry } from '@pimcore/studio-ui-bundle/modules/element'
import { useLanguageSelection } from '@pimcore/studio-ui-bundle/modules/data-object'
import { api, type AdvancedColumnConfig } from '@pimcore/studio-ui-bundle/api/data-object'
import { createColumnHelper } from '@tanstack/react-table'
import { type AdvancedEditorColumn } from './types'

export interface ColumnPreviewProps {
  column: AdvancedEditorColumn
  pipelineValue?: Record<string, any>
}

const columnHelper = createColumnHelper()

export const PreviewGrid = ({ value }: { value: Array<{ type: string, value: any }> }): React.JSX.Element => {
  const advancedGridCellRegistry = useInjection<DynamicTypeGridCellRegistry>(
    serviceIds['DynamicTypes/AdvancedGridCellRegistry']
  )

  const columns = value.map((item, index) => {
    const isAdvancedCellType = advancedGridCellRegistry.hasDynamicType(item.type)
    const safeKey = `${item.type.replace(/\./g, '_')}-${index}`

    return columnHelper.accessor(safeKey, {
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
    const safeKey = `${item.type.replace(/\./g, '_')}-${index}`
    row[safeKey] = item.value
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
  const { currentLanguage: contextLanguage } = useLanguageSelection()
  const { language: previewLanguage } = usePreviewMode()
  const currentLanguage = previewLanguage ?? contextLanguage

  const pipeline = (pipelineValue !== undefined && Object.keys(pipelineValue).length > 0) ? pipelineValue : column.pipeline

  // Resolve locale: explicit per-column override > global language (only for localizable columns)
  const resolvedLocale = column.localizable === true
    ? (column.locale ?? currentLanguage)
    : undefined

  const { data, error, isFetching } = api.endpoints.dataObjectGetGridPreview.useQuery({
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

  // Keep the last successful data so re-fetches don't flash "no data"
  const lastData = useRef(data)
  if (data !== undefined) lastData.current = data

  if (error !== undefined) {
    const message = 'error' in (error as object) ? (error as any).error : t('data-hub.column-config-modal.preview.error')
    return <Text type='danger'>{ message }</Text>
  }

  if (isFetching && lastData.current === undefined) {
    return <Text type='secondary'>{ t('data-hub.column-config-modal.preview.loading') }</Text>
  }

  const value = lastData.current?.value

  if (value === undefined || value === null || !Array.isArray(value) || value.length === 0) {
    return <Text type='secondary'>{ t('data-hub.column-config-modal.preview.no-data') }</Text>
  }

  return <PreviewGrid value={ value } />
}

const getPreviewItemId = (item: ReturnType<typeof usePreviewMode>['item']): number | undefined => {
  const id = item?.data?.id
  return id === undefined ? undefined : Number(id)
}

export const ColumnPreview = ({ column, pipelineValue }: ColumnPreviewProps): React.JSX.Element => {
  const { t } = useTranslation()
  const { item } = usePreviewMode()
  const objectId = getPreviewItemId(item)

  // Debounce pipeline changes so the previous result stays visible during edits
  const [debouncedPipelineValue, setDebouncedPipelineValue] = useState(pipelineValue)
  useEffect(() => {
    const timer = setTimeout(() => { setDebouncedPipelineValue(pipelineValue) }, 300)
    return () => { clearTimeout(timer) }
  }, [pipelineValue])

  // The shared slot renders its child only while preview mode is on, so nothing is requested while it is off
  return (
    <PreviewModeColumnPreview>
      { () => objectId === undefined
        ? (
          <Text type='secondary'>
            { t('data-hub.column-config-modal.preview.no-item') }
          </Text>
          )
        : (
          <PreviewResult
            column={ column }
            objectId={ objectId }
            pipelineValue={ debouncedPipelineValue }
          />
          ) }
    </PreviewModeColumnPreview>
  )
}

/** Preview row of a collapsed advanced card: value from the stored pipeline, no request without a config. */
export const CollapsedColumnPreview = ({ column }: { column: AdvancedEditorColumn }): React.JSX.Element => {
  const { t } = useTranslation()

  if (column.pipelineConfig === undefined || column.pipeline === undefined) {
    const key = column.pipelineConfig === undefined ? 'loading' : 'no-data'
    return (
      <PreviewModeColumnPreview>
        { () => <Text type='secondary'>{ t(`data-hub.column-config-modal.preview.${key}`) }</Text> }
      </PreviewModeColumnPreview>
    )
  }

  return (
    <ColumnPreview
      column={ column }
      pipelineValue={ column.pipeline }
    />
  )
}
