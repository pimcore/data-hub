/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { useState, useMemo } from 'react'
import { IconTextButton, Select, Modal, OperationalGrid, Accordion, type AccordionItemType } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { isNil } from 'lodash'
import { type QueryEntity, type MutationEntity } from './types'
import { useClassDefinitions } from '@pimcore/studio-ui-bundle/modules/data-object'

interface SchemaAccordionProps {
  type: 'query' | 'mutation'
  value?: QueryEntity[] | MutationEntity[]
  onChange?: (value: QueryEntity[] | MutationEntity[]) => void
}

export const SchemaAccordion = ({
  type,
  value = [],
  onChange
}: SchemaAccordionProps): React.JSX.Element => {
  const { t } = useTranslation()
  const [modalOpen, setModalOpen] = useState(false)
  const [selectedClasses, setSelectedClasses] = useState<string[]>([])

  const { getAllClassDefinitions } = useClassDefinitions()
  const availableClasses = useMemo(() => {
    return getAllClassDefinitions()
  }, [getAllClassDefinitions])

  const currentEntityNames = useMemo(() => {
    return value.map((entity) => entity.entity)
  }, [value])

  const options = useMemo(() => {
    return availableClasses
      .filter(cls => !currentEntityNames.includes(cls.name as string))
      .map(cls => ({
        value: cls.name,
        label: cls.name,
        searchValue: cls.name
      }))
  }, [availableClasses, currentEntityNames])

  const handleOpenModal = (): void => {
    setSelectedClasses([])
    setModalOpen(true)
  }

  const handleCancel = (): void => {
    setSelectedClasses([])
    setModalOpen(false)
  }

  const handleApply = (): void => {
    if (selectedClasses.length > 0 && !isNil(onChange)) {
      const newEntities = selectedClasses.map(className => {
        if (type === 'query') {
          return {
            id: className,
            entity: className
          }
        } else {
          return {
            id: className,
            entity: className,
            create: false,
            update: true,
            delete: false
          }
        }
      })
      onChange([...value, ...newEntities])
    }
    setSelectedClasses([])
    setModalOpen(false)
  }

  const accordionItem: AccordionItemType = useMemo(() => ({
    key: type,
    id: type,
    title: <>{t(`data-hub.schema.${type}-schema`)}</>,
    info: (
      <IconTextButton
        icon={ { value: 'add-find' } }
        onClick={ (e) => {
          e.stopPropagation()
          handleOpenModal()
        } }
      >
        {t('add')}
      </IconTextButton>
    ),
    children: (
      <OperationalGrid.Grid />
    )
  }), [type, t])

  return (
    <>
      <Accordion
        activeKey={ type }
        bordered
        collapsible="icon"
        items={ [accordionItem] }
        size="small"
        table
      />
      <Modal
        cancelText={ t('button.cancel') }
        okText={ t('button.apply') }
        onCancel={ handleCancel }
        onOk={ handleApply }
        open={ modalOpen }
        size="M"
        title={ t(`data-hub.schema.${type}-schema`) }
      >
        <Select
          listHeight={ 150 }
          mode="multiple"
          onChange={ (values) => { setSelectedClasses(values as string[]) } }
          optionFilterProp="searchValue"
          options={ options }
          placeholder={ t('data-hub.schema.select-class') }
          showSearch
          style={ { width: '100%' } }
          value={ selectedClasses }
        />
      </Modal>
    </>
  )
}
