/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { createContext, useContext, useMemo } from 'react'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import {
  ColumnPreview as PreviewModeColumnPreview,
  Grid,
  GridContentRenderer,
  Spin,
  Text,
  usePreviewMode
} from '@pimcore/studio-ui-bundle/components'
import { api, type GridColumnConfiguration } from '@pimcore/studio-ui-bundle/api/data-object'
import { useLanguageSelection } from '@pimcore/studio-ui-bundle/modules/data-object'
import { createColumnHelper } from '@tanstack/react-table'
import { ADVANCED_COLUMN_KEY, ADVANCED_COLUMN_TYPE, type AdvancedEditorColumn } from './types'

interface SimpleColumnPreviewsContextValue {
  isLoading: boolean
  hasError: boolean
  values: Map<string, unknown>
  hasItem: boolean
  getLocale: (column: AdvancedEditorColumn) => string | null | undefined
}

const SimpleColumnPreviewsContext = createContext<SimpleColumnPreviewsContextValue | undefined>(undefined)

const ADAPTER_TYPES = new Set(['dataobject.adapter', 'dataobject.objectbrick', 'dataobject.classificationstore'])

export const isSimpleColumn = (column: { key: string, type: string }): boolean =>
  column.key !== ADVANCED_COLUMN_KEY && column.type !== ADVANCED_COLUMN_TYPE

const valueKey = (key: string, locale: string | null | undefined): string => `${key}|${locale ?? ''}`

export interface SimpleColumnPreviewsProviderProps {
  classId: string
  /** The draft columns of the editor; the advanced ones are left out of the request */
  columns: AdvancedEditorColumn[]
  availableFields: GridColumnConfiguration[]
  children?: React.ReactNode
}

/** Loads the values of all simple columns for the preview item with a single grid request */
export const SimpleColumnPreviewsProvider = ({
  classId,
  columns,
  availableFields,
  children
}: SimpleColumnPreviewsProviderProps): React.JSX.Element => {
  const { enabled, item, language: previewLanguage } = usePreviewMode()
  const { currentLanguage: contextLanguage } = useLanguageSelection()
  const currentLanguage = previewLanguage ?? contextLanguage
  const itemId = item?.data?.id

  const getLocale = (column: AdvancedEditorColumn): string | null | undefined => {
    if (column.localizable !== true) return undefined
    const locale = column.locale ?? currentLanguage
    return locale === 'default' ? null : locale
  }

  const requestColumns = useMemo(() => columns.filter(isSimpleColumn).map(column => {
    const available = availableFields.find(field => field.key === column.key)
    return {
      key: column.key,
      type: column.type,
      locale: getLocale(column),
      group: available?.group as unknown as string[] | undefined,
      config: available?.config
    }
  }), [columns, availableFields, currentLanguage])

  const skip = !enabled || itemId === undefined || requestColumns.length === 0

  const { data, isFetching, error } = api.endpoints.dataObjectGetGrid.useQuery({
    classId,
    body: {
      folderId: 1,
      columns: requestColumns as any,
      filters: {
        page: 1,
        pageSize: 1,
        includeDescendants: true,
        columnFilters: [{ type: 'system.ids', filterValue: [itemId] }]
      }
    }
  } as any, { skip })

  const value = useMemo(() => {
    const values = new Map<string, unknown>()
    ;(data?.items?.[0]?.columns ?? []).forEach((column: any) => {
      values.set(valueKey(String(column.key), column.locale as string | null | undefined), column.value)
    })
    return { isLoading: isFetching && data === undefined, hasError: error !== undefined, values, hasItem: itemId !== undefined, getLocale }
  }, [isFetching, data, error, itemId, currentLanguage])

  return <SimpleColumnPreviewsContext.Provider value={ value }>{ children }</SimpleColumnPreviewsContext.Provider>
}

const columnHelper = createColumnHelper()

export interface SimpleColumnPreviewProps {
  column: AdvancedEditorColumn
  availableField?: GridColumnConfiguration
}

/** The value of a simple column for the preview item, rendered with the grid's cell renderers */
export const SimpleColumnPreview = ({ column, availableField }: SimpleColumnPreviewProps): React.JSX.Element | null => {
  const context = useContext(SimpleColumnPreviewsContext)
  const { t } = useTranslation()

  if (context === undefined) return null

  const { isLoading, hasError, values, hasItem, getLocale } = context
  const key = valueKey(column.key, getLocale(column))

  return (
    <PreviewModeColumnPreview>
      { () => {
        if (!hasItem) {
          return <Text type='secondary'>{ t('data-hub.column-config-modal.preview.no-item') }</Text>
        }
        if (isLoading) {
          return <Spin size='small' />
        }
        if (hasError) {
          return <Text type='danger'>{ t('data-hub.column-config-modal.preview.error') }</Text>
        }
        if (!values.has(key)) {
          return <Text type='secondary'>{ t('data-hub.column-config-modal.preview.no-data') }</Text>
        }

        const isAdapter = ADAPTER_TYPES.has(column.type)
        const config = availableField?.config as Record<string, any> | undefined
        const gridColumn = columnHelper.accessor('value', {
          meta: {
            editable: false,
            type: column.type,
            config: isAdapter
              ? {
                  dataObjectType: config?.fieldDefinition?.fieldtype ?? (availableField as any)?.frontendType ?? column.fieldtype,
                  dataObjectConfig: { ...config }
                }
              : {}
          }
        })

        return (
          <GridContentRenderer>
            <Grid
              autoWidth
              columns={ [gridColumn] }
              data={ [{ value: values.get(key) }] }
              hideColumnHeaders
            />
          </GridContentRenderer>
        )
      } }
    </PreviewModeColumnPreview>
  )
}
