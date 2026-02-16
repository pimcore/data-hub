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
import { IconTextButton, OperationalGrid, Accordion, type AccordionItemType } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { isNil } from 'lodash'
import { type Workspace, type WorkspaceType } from './types'

interface WorkspaceAccordionProps {
  type: WorkspaceType
  value?: Workspace[]
  onChange?: (value: Workspace[]) => void
}

export const WorkspaceAccordion = ({
  type,
  value = [],
  onChange
}: WorkspaceAccordionProps): React.JSX.Element => {
  const { t } = useTranslation()

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
    title: <>{t(`data-hub.workspaces.${type}`)}</>,
    info: (
      <IconTextButton
        icon={ { value: 'add-find' } }
        onClick={ (e) => {
          e.stopPropagation()
          handleAdd()
        } }
      >
        {t('add')}
      </IconTextButton>
    ),
    children: (
      <OperationalGrid.Grid />
    )
  }), [type, value, handleAdd, t])

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
