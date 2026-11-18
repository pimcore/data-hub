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
import { IconTextButton, Modal, OperationalGrid, Accordion, Button, Checkbox, Flex, NoContent, SearchInput, type AccordionItemType } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { isNil } from 'lodash'
import { type QueryEntity, type MutationEntity } from './types'
import { useClassDefinitions } from '@pimcore/studio-ui-bundle/modules/data-object'
import { filterClassNames, selectClassNames, deselectClassNames, orderLike } from './class-picker-selection'

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
  const [searchValue, setSearchValue] = useState('')

  const { getAllClassDefinitions } = useClassDefinitions()
  const availableClasses = useMemo(() => {
    return getAllClassDefinitions()
  }, [getAllClassDefinitions])

  const addedClassNames = useMemo(() => {
    return new Set<string>(value.map((entity) => entity.entity))
  }, [value])

  const classNames = useMemo((): string[] => {
    return availableClasses.map(cls => cls.name as string)
  }, [availableClasses])

  const visibleClassNames = useMemo(() => {
    return filterClassNames(classNames, searchValue)
  }, [classNames, searchValue])

  const closeModal = (): void => {
    setSelectedClasses([])
    setSearchValue('')
    setModalOpen(false)
  }

  const handleOpenModal = (): void => {
    setSelectedClasses([])
    setSearchValue('')
    setModalOpen(true)
  }

  const handleCancel = (): void => {
    closeModal()
  }

  const toggleClass = (className: string, checked: boolean): void => {
    setSelectedClasses(current => checked
      ? selectClassNames(current, [className], addedClassNames)
      : deselectClassNames(current, [className]))
  }

  const handleApply = (): void => {
    if (selectedClasses.length > 0 && !isNil(onChange)) {
      const newEntities = orderLike(selectedClasses, classNames).map(className => {
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
    closeModal()
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
        size="L"
        title={ t(`data-hub.schema.${type}-schema`) }
      >
        <Flex
          gap="small"
          vertical
        >
          <SearchInput
            aria-label={ t('data-hub.schema.search-classes') }
            onChange={ (e) => { setSearchValue(e.target.value) } }
            placeholder={ t('data-hub.schema.search-classes') }
            style={ { width: '100%', maxWidth: '100%' } }
            value={ searchValue }
            withoutAddon
          />
          <Flex gap="small">
            <Button
              onClick={ () => { setSelectedClasses(current => selectClassNames(current, visibleClassNames, addedClassNames)) } }
              size="small"
            >
              {t('data-hub.schema.select-all')}
            </Button>
            <Button
              onClick={ () => { setSelectedClasses(current => deselectClassNames(current, visibleClassNames)) } }
              size="small"
            >
              {t('data-hub.schema.deselect-all')}
            </Button>
          </Flex>
          {visibleClassNames.length === 0
            ? <NoContent text={ t('data-hub.schema.no-classes-found') } />
            : (
              <div style={ { maxHeight: '50vh', overflowY: 'auto' } }>
                <Flex
                  gap="mini"
                  vertical
                >
                  {visibleClassNames.map(className => (
                    <Checkbox
                      checked={ addedClassNames.has(className) || selectedClasses.includes(className) }
                      disabled={ addedClassNames.has(className) }
                      key={ className }
                      onChange={ (e) => { toggleClass(className, e.target.checked) } }
                    >
                      {className}
                    </Checkbox>
                  ))}
                </Flex>
              </div>
              )}
        </Flex>
      </Modal>
    </>
  )
}
