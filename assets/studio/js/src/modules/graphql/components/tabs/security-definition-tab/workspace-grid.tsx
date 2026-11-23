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
import { Flex, OperationalGrid, IconButton } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { createColumnHelper } from '@tanstack/react-table'
import { WorkspaceAccordion } from './workspace-accordion'
import { isNil } from 'lodash'
import { type Workspace, type WorkspaceType } from './types'
import { RowMarkCell } from '../schema-definition-tab/row-marks'
import { useWorkspaceRowChanges, workspacePathOf } from '../../../change-control/workspace-row-changes'

interface WorkspaceGridProps {
  type: WorkspaceType
  value?: Workspace[]
  onChange?: (value: Workspace[]) => void
  isWriteable?: boolean
}

export const WorkspaceGrid = ({ type, value = [], onChange, isWriteable = true }: WorkspaceGridProps): React.JSX.Element => {
  const { t } = useTranslation()

  // Map workspace type to link type for the column
  const linkType = type === 'documents' ? 'document-link' : type === 'assets' ? 'asset-link' : 'object-link'

  // a review marks each row the list adds, changes or drops; a dropped row is shown after the proposed ones
  const rowChanges = useWorkspaceRowChanges(type)
  const rows = useMemo(
    () => rowChanges === undefined ? value : [...value, ...rowChanges.removed],
    [value, rowChanges]
  )

  const columns = useMemo(() => {
    const columnHelper = createColumnHelper<Workspace>()

    return [
      ...(rowChanges !== undefined
        ? [{
            id: 'review',
            header: '',
            size: 100,
            cell: (info: { row: { index: number } }) => (
              <RowMarkCell
                status={ info.row.index >= value.length
                  ? 'removed'
                  : rowChanges.byPath[workspacePathOf(value[info.row.index]?.path)] }
              />
            ),
            enableResizing: false,
            enableSorting: false
          }]
        : []),
      columnHelper.accessor('path', {
        header: t('data-hub.workspaces.path'),
        size: 300,
        meta: {
          editable: isWriteable,
          type: linkType,
          autoWidth: true
        }
      }),
      columnHelper.accessor('create', {
        header: t('data-hub.workspaces.create'),
        size: 80,
        meta: {
          type: 'checkbox',
          editable: isWriteable,
          config: {
            align: 'center',
            disabled: !isWriteable
          }
        }
      }),
      columnHelper.accessor('read', {
        header: t('data-hub.workspaces.read'),
        size: 80,
        meta: {
          type: 'checkbox',
          editable: isWriteable,
          config: {
            align: 'center',
            disabled: !isWriteable
          }
        }
      }),
      columnHelper.accessor('update', {
        header: t('data-hub.workspaces.update'),
        size: 80,
        meta: {
          type: 'checkbox',
          editable: isWriteable,
          config: {
            align: 'center',
            disabled: !isWriteable
          }
        }
      }),
      columnHelper.accessor('delete', {
        header: t('data-hub.workspaces.delete'),
        size: 80,
        meta: {
          type: 'checkbox',
          editable: isWriteable,
          config: {
            align: 'center',
            disabled: !isWriteable
          }
        }
      }),
      ...(isWriteable
        ? [{
            id: 'actions',
            header: '',
            size: 60,
            cell: (info: { row: { index: number } }) => (
              <Flex
                align="center"
                justify="center"
              >
                <IconButton
                  icon={ { value: 'trash' } }
                  onClick={ () => {
                    const newData = [...value]
                    newData.splice(info.row.index, 1)
                    if (!isNil(onChange)) {
                      onChange(newData)
                    }
                  } }
                  type="link"
                />
              </Flex>
            ),
            enableResizing: false,
            enableSorting: false
          }]
        : [])
    ]
  }, [linkType, t, value, onChange, isWriteable, rowChanges])

  return (
    <OperationalGrid
      autoWidth
      columns={ columns }
      onChange={ onChange }
      value={ rows }
    >
      <OperationalGrid.Operations>
        {() => (
          <WorkspaceAccordion
            isWriteable={ isWriteable }
            onChange={ onChange }
            type={ type }
            value={ value }
          />
        )}
      </OperationalGrid.Operations>
    </OperationalGrid>
  )
}
