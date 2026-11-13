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
import { ColumnPicker, Flex, Header, IconButton, type ColumnPickerGroup } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { type GridColumnConfiguration } from '@pimcore/studio-ui-bundle/api/data-object'
import { ClassificationStoreFieldPickerProvider, useClassificationStoreFieldPicker } from '@pimcore/studio-ui-bundle/modules/element'
import { useStyles } from './base-column-editor.styles'
import { PICK_KEY_SUFFIX, classificationStoreId } from './column-identity'

export interface FieldsToAddPanelProps {
  /** The class the picked store keys are scoped to */
  classId?: string
  groups: Array<ColumnPickerGroup<GridColumnConfiguration>>
  /** The available-columns request failed: an empty tree then says nothing about the class */
  isError?: boolean
  onColumnSelect: (column: GridColumnConfiguration) => void
  onKeySelect: (column: GridColumnConfiguration, ids: { groupId: number, keyId: number }) => void
  onClose: () => void
}

/**
 * Embedded "Fields to add" panel shown inside the migrated column editor: the
 * searchable ColumnPicker tree with a collapsible header.
 */
const FieldsToAddPanelContent = ({ groups, isError = false, onColumnSelect, onKeySelect, onClose }: FieldsToAddPanelProps): React.JSX.Element => {
  const { t } = useTranslation()
  const { styles } = useStyles()
  const { openPicker } = useClassificationStoreFieldPicker()

  return (
    <div className={ styles.fieldsPanel }>
      <Header
        fullWidth
        title={ t('data-hub.column-config-modal.fields-to-add') }
      >
        <Flex
          className='w-full'
          justify='flex-end'
        >
          <IconButton
            icon={ { value: 'collapse-sidebar', colorToken: 'colorPrimary' } }
            onClick={ onClose }
          />
        </Flex>
      </Header>

      { /* The failure itself is reported by the editor body, which stays visible when this panel is collapsed */ }
      { !isError && (
        <ColumnPicker
          fillHeight
          groups={ groups }
          onSelect={ (item: { key: string, meta?: GridColumnConfiguration }) => {
            const field = item.meta
            if (field === undefined) return
            if (item.key === `${field.key}${PICK_KEY_SUFFIX}`) {
              openPicker({
                storeId: classificationStoreId(field.config),
                fieldName: field.key,
                onPick: ({ groupId, keyId }) => { onKeySelect(field, { groupId, keyId }) }
              })
              return
            }
            onColumnSelect(field)
          } }
        />
      ) }
    </div>
  )
}

export const FieldsToAddPanel = (props: FieldsToAddPanelProps): React.JSX.Element => (
  <ClassificationStoreFieldPickerProvider defaultClassId={ props.classId }>
    <FieldsToAddPanelContent { ...props } />
  </ClassificationStoreFieldPickerProvider>
)
