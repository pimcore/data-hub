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
import { SchemaAccordion } from './schema-accordion'
import { isNil } from 'lodash'
import { type MutationEntity } from './types'

interface MutationGridProps {
  value?: MutationEntity[]
  onChange?: (value: MutationEntity[]) => void
}

export const MutationGrid = ({ value = [], onChange }: MutationGridProps): React.JSX.Element => {
  const { t } = useTranslation()

  const columns = useMemo(() => {
    const columnHelper = createColumnHelper<MutationEntity>()

    return [
      columnHelper.accessor('entity', {
        header: t('data-hub.schema.entity'),
        size: 200,
        meta: {
          type: 'input-text',
          autoWidth: true
        }
      }),
      columnHelper.accessor('create', {
        header: t('data-hub.workspaces.create'),
        size: 80,
        meta: {
          type: 'checkbox',
          editable: true,
          config: {
            align: 'center'
          }
        }
      }),
      columnHelper.accessor('update', {
        header: t('data-hub.workspaces.update'),
        size: 80,
        meta: {
          type: 'checkbox',
          editable: true,
          config: {
            align: 'center'
          }
        }
      }),
      columnHelper.accessor('delete', {
        header: t('data-hub.workspaces.delete'),
        size: 80,
        meta: {
          type: 'checkbox',
          editable: true,
          config: {
            align: 'center'
          }
        }
      }),
      {
        id: 'settings',
        header: t('data-hub.schema.settings'),
        size: 100,
        cell: () => {
          return (
            <Flex
              align="center"
              justify="center"
            >
              <IconButton
                icon={ { value: 'settings' } }
                onClick={ () => {} }
                type="link"
              />
            </Flex>
          )
        }
      },
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
        )
      }
    ]
  }, [t, value, onChange])

  return (
    <OperationalGrid
      autoWidth
      columns={ columns }
      onChange={ onChange }
      value={ value }
    >
      <OperationalGrid.Operations>
        {() => (
          <SchemaAccordion
            onChange={ onChange }
            type="mutation"
            value={ value }
          />
        )}
      </OperationalGrid.Operations>
    </OperationalGrid>
  )
}
