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
import { PermissionAccordion } from './permission-accordion'
import { isNil } from 'lodash'
import { type Permission } from './types'

interface PermissionGridProps {
  type: 'roles' | 'users'
  value?: Permission[]
  onChange?: (value: Permission[]) => void
  isWriteable?: boolean
}

export const PermissionGrid = ({ type, value = [], onChange, isWriteable = true }: PermissionGridProps): React.JSX.Element => {
  const { t } = useTranslation()

  const itemType = type === 'roles' ? 'role' : 'user'

  const columns = useMemo(() => {
    const columnHelper = createColumnHelper<Permission>()

    return [
      columnHelper.accessor('name', {
        header: itemType === 'role' ? t('data-hub.permissions.role') : t('data-hub.permissions.user'),
        size: 300,
        meta: {
          editable: false,
          autoWidth: true
        }
      }),
      columnHelper.accessor('read', {
        header: t('data-hub.permissions.read'),
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
        header: t('data-hub.permissions.update'),
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
        header: t('data-hub.permissions.delete'),
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
      {
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
      }
    ]
  }, [itemType, t, value, onChange, isWriteable])

  return (
    <OperationalGrid
      autoWidth
      columns={ columns }
      onChange={ onChange }
      value={ value }
    >
      <OperationalGrid.Operations>
        {() => (
          <PermissionAccordion
            onChange={ onChange }
            type={ type }
            value={ value }
          />
        )}
      </OperationalGrid.Operations>
    </OperationalGrid>
  )
}
