/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { useCallback, useRef } from 'react'
import { ColumnPicker, Flex, Header, IconButton, type ColumnPickerGroup } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { type GridColumnConfiguration } from '@pimcore/studio-ui-bundle/api/data-object'
import { ClassificationStoreModalProvider, TabId, useClassificationStoreModal } from '@pimcore/studio-ui-bundle/modules/element'
import { useStyles } from './base-column-editor.styles'
import { PICK_KEY_SUFFIX, classificationStoreId, firstNonEmpty } from './column-identity'

export interface FieldsToAddPanelProps {
  /** The class the picked store keys are scoped to */
  classId?: string
  groups: Array<ColumnPickerGroup<GridColumnConfiguration>>
  /** The available-columns request failed: an empty tree then says nothing about the class */
  isError?: boolean
  onColumnSelect: (column: GridColumnConfiguration) => void
  onKeySelect: (
    column: GridColumnConfiguration,
    keys: Array<{ groupId: number, keyId: number, keyTitle?: string }>
  ) => void
  onClose: () => void
}

/**
 * One selected row of the key tab: the group it was picked under, plus the key's own layout. The
 * modal reports the whole layout, so the key's resolved title comes with it.
 */
interface SelectedKeyRow {
  groupId: number
  id: number
  name?: string
  title?: string | null
}

/**
 * Embedded "Fields to add" panel shown inside the migrated column editor: the
 * searchable ColumnPicker tree with a collapsible header.
 */
const FieldsToAddPanelContent = ({ classId, groups, isError = false, onColumnSelect, onKeySelect, onClose }: FieldsToAddPanelProps): React.JSX.Element => {
  const { t } = useTranslation()
  const { styles } = useStyles()
  const pickingForRef = useRef<GridColumnConfiguration | undefined>(undefined)

  // The modal reports the whole applied selection in one update, so every picked key is added at once.
  const onUpdate = useCallback((data: { data?: SelectedKeyRow[] }): void => {
    const field = pickingForRef.current
    pickingForRef.current = undefined
    const rows = data?.data ?? []

    if (field === undefined || rows.length === 0) return

    onKeySelect(field, rows.map((row) => ({
      groupId: row.groupId,
      keyId: row.id,
      // the title falls back to the key name here, as the relations lookup does on the stored path
      keyTitle: firstNonEmpty(row.title, row.name)
    })))
  }, [onKeySelect])

  const { openModal } = useClassificationStoreModal({ onUpdate })

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
              pickingForRef.current = field
              openModal({
                storeId: classificationStoreId(field.config),
                classId: classId ?? '',
                fieldName: field.key,
                name: field.key,
                allowedTabs: [TabId.GroupByKey],
                // Several keys of one store are added in a single trip through the modal.
                singleSelection: false
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

/**
 * The modal is driven here rather than through the SDK's ClassificationStoreFieldPickerProvider:
 * that provider hardcodes `singleSelection: true`, and widening it would need a studio-ui-bundle
 * canary. Everything it does beyond that — a nested provider so this modal does not clash with the
 * one the regular "add column" flow drives — is what the wrapper below gives us.
 */
export const FieldsToAddPanel = (props: FieldsToAddPanelProps): React.JSX.Element => (
  <ClassificationStoreModalProvider>
    <FieldsToAddPanelContent { ...props } />
  </ClassificationStoreModalProvider>
)
