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
import { Flex, IconTextButton, OperationalGrid, Accordion, type AccordionItemType } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { isNil } from 'lodash'
import { type Workspace, type WorkspaceType } from './types'
import { RowMark } from '../schema-definition-tab/row-marks'
import { useFormItemAnnotation } from '../../../change-control/studio-form-annotations'
import { useWorkspaceRowChanges } from '../../../change-control/workspace-row-changes'

interface WorkspaceAccordionProps {
  type: WorkspaceType
  value?: Workspace[]
  onChange?: (value: Workspace[]) => void
  isWriteable?: boolean
}

export const WorkspaceAccordion = ({
  type,
  value = [],
  onChange,
  isWriteable = true
}: WorkspaceAccordionProps): React.JSX.Element => {
  const { t } = useTranslation()
  // the grid's Form.Item is noStyle, which renders no mark: a review marks the list's header
  const annotation = useFormItemAnnotation(['workspaces', type])
  const rowChanges = useWorkspaceRowChanges(type)
  const status = annotation?.status ?? rowChanges?.status

  const handleAdd = (): void => {
    if (!isNil(onChange)) {
      onChange([...value, {
        path: '',
        create: false,
        read: true,
        update: false,
        delete: false
      }])
    }
  }

  const accordionItem: AccordionItemType = useMemo(() => ({
    key: type,
    id: type,
    title: (
      <Flex
        align="center"
        gap="small"
      >
        {t(`data-hub.workspaces.${type}`)}
        <RowMark status={ status } />
      </Flex>
    ),
    info: isWriteable
      ? (
        <IconTextButton
          icon={ { value: 'add-find' } }
          onClick={ (e) => {
            e.stopPropagation()
            handleAdd()
          } }
        >
          {t('add')}
        </IconTextButton>
        )
      : undefined,
    children: (
      <OperationalGrid.Grid />
    )
  }), [type, value, handleAdd, t, isWriteable, status])

  return (
    <Accordion
      activeKey={ type }
      bordered
      collapsible="icon"
      items={ [accordionItem] }
      size="small"
      table
    />
  )
}
