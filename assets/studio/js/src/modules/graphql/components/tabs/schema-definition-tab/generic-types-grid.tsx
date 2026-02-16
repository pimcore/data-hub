/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { useMemo } from 'react'
import { Flex, OperationalGrid, Accordion, type AccordionItemType } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { createColumnHelper } from '@tanstack/react-table'
import { type GenericType } from './types'

interface GenericTypesGridProps {
  value?: GenericType[]
  onChange?: (value: GenericType[]) => void
  isWriteable?: boolean
}

export const GenericTypesGrid = ({ value = [], onChange, isWriteable = true }: GenericTypesGridProps): React.JSX.Element => {
  const { t } = useTranslation()

  const columns = useMemo(() => {
    const columnHelper = createColumnHelper<GenericType>()

    return [
      columnHelper.accessor('name', {
        header: '',
        size: 200,
        meta: {
          autoWidth: true
        },
        cell: (info) => {
          const entityName = info.getValue()
          return (
            <Flex
              align="center"
              style={ { paddingLeft: '12px' } }
            >
              {t(`data-hub.schema.special.${entityName}`)}
            </Flex>
          )
        }
      }),
      columnHelper.accessor('create', {
        header: t('data-hub.workspaces.create'),
        size: 80,
        meta: {
          type: 'checkbox',
          editable: (row: GenericType) => isWriteable && row.createPossible,
          tooltip: (row: GenericType) => !isWriteable ? null : (row.createPossible ? null : t('data-hub.schema.operation-not-implemented')),
          config: {
            align: 'center',
            disabled: (row: GenericType) => !isWriteable || !row.createPossible
          }
        }
      }),
      columnHelper.accessor('read', {
        header: t('data-hub.workspaces.read'),
        size: 80,
        meta: {
          type: 'checkbox',
          editable: (row: GenericType) => isWriteable && row.readPossible,
          tooltip: (row: GenericType) => !isWriteable ? null : (row.readPossible ? null : t('data-hub.schema.operation-not-implemented')),
          config: {
            align: 'center',
            disabled: (row: GenericType) => !isWriteable || !row.readPossible
          }
        }
      }),
      columnHelper.accessor('update', {
        header: t('data-hub.workspaces.update'),
        size: 80,
        meta: {
          type: 'checkbox',
          editable: (row: GenericType) => isWriteable && row.updatePossible,
          tooltip: (row: GenericType) => !isWriteable ? null : (row.updatePossible ? null : t('data-hub.schema.operation-not-implemented')),
          config: {
            align: 'center',
            disabled: (row: GenericType) => !isWriteable || !row.updatePossible
          }
        }
      }),
      columnHelper.accessor('delete', {
        header: t('data-hub.workspaces.delete'),
        size: 80,
        meta: {
          type: 'checkbox',
          editable: (row: GenericType) => isWriteable && row.deletePossible,
          tooltip: (row: GenericType) => !isWriteable ? null : (row.deletePossible ? null : t('data-hub.schema.operation-not-implemented')),
          config: {
            align: 'center',
            disabled: (row: GenericType) => !isWriteable || !row.deletePossible
          }
        }
      })
    ]
  }, [t, isWriteable])

  const accordionItem: AccordionItemType = useMemo(() => ({
    key: 'genericTypes',
    id: 'genericTypes',
    title: <>{t('data-hub.schema.generic-types')}</>,
    children: (
      <OperationalGrid.Grid />
    )
  }), [t])

  return (
    <OperationalGrid
      autoWidth
      columns={ columns }
      onChange={ onChange }
      value={ value }
    >
      <OperationalGrid.Operations>
        {() => (
          <Accordion
            activeKey="genericTypes"
            bordered
            collapsible="icon"
            items={ [accordionItem] }
            size="small"
            table
          />
        )}
      </OperationalGrid.Operations>
    </OperationalGrid>
  )
}
