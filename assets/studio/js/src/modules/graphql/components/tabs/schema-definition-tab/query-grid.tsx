/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { useMemo, useState } from 'react'
import { Flex, OperationalGrid, IconButton } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { createColumnHelper } from '@tanstack/react-table'
import { SchemaAccordion } from './schema-accordion'
import { isNil } from 'lodash'
import { type QueryEntity } from './types'
import { SchemaFieldsModal } from './schema-fields-modal/schema-fields-modal'
import { bundleServiceIds } from '../../../../../config/service-ids'
import { RowMark, useRowMarks, withRemovedRows } from './row-marks'

interface QueryGridProps {
  value?: QueryEntity[]
  onChange?: (value: QueryEntity[]) => void
  isWriteable?: boolean
}

export const QueryGrid = ({ value = [], onChange, isWriteable = true }: QueryGridProps): React.JSX.Element => {
  const { t } = useTranslation()
  const [modalOpen, setModalOpen] = useState(false)
  const [selectedEntity, setSelectedEntity] = useState<QueryEntity | null>(null)

  const marks = useRowMarks('query')
  const rows = useMemo(() => withRemovedRows(value, marks), [value, marks])

  const columns = useMemo(() => {
    const columnHelper = createColumnHelper<QueryEntity>()

    return [
      ...(Object.keys(marks).length > 0
        ? [{
            id: 'review',
            header: '',
            size: 100,
            cell: (info: { row: { index: number } }) => <RowMark status={ marks[rows[info.row.index]?.id] } />
          }]
        : []),
      columnHelper.accessor('entity', {
        header: t('data-hub.schema.entity'),
        size: 300,
        meta: {
          type: 'input-text',
          editable: isWriteable,
          autoWidth: true
        }
      }),
      {
        id: 'settings',
        header: t('data-hub.schema.settings'),
        size: 100,
        cell: (info) => {
          return (
            <Flex
              align="center"
              justify="center"
            >
              { marks[rows[info.row.index]?.id] !== 'removed' && (
              <IconButton
                disabled={ false }
                icon={ { value: 'settings' } }
                onClick={ () => {
                  setSelectedEntity(rows[info.row.index])
                  setModalOpen(true)
                } }
                type="link"
              />
              ) }
            </Flex>
          )
        }
      },
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
            )
          }]
        : [])
    ]
  }, [t, value, onChange, isWriteable, marks, rows])

  return (
    <>
      <OperationalGrid
        autoWidth
        columns={ columns }
        onChange={ onChange }
        value={ rows }
      >
        <OperationalGrid.Operations>
          {() => (
            <SchemaAccordion
              isWriteable={ isWriteable }
              onChange={ onChange }
              type="query"
              value={ value }
            />
          )}
        </OperationalGrid.Operations>
      </OperationalGrid>

      {modalOpen && (
        <SchemaFieldsModal
          className={ selectedEntity?.entity ?? '' }
          disabled={ !isWriteable }
          onApply={ () => { setModalOpen(false) } }
          onCancel={ () => { setModalOpen(false) } }
          open={ modalOpen }
          operatorRegistryServiceId={ bundleServiceIds['DataHub/DynamicTypes/Operator/GraphQL/QueryRegistry'] }
        />
      )}
    </>
  )
}
