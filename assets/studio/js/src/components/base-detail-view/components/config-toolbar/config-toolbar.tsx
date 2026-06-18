/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { type ReactNode } from 'react'
import { Button, ButtonGroup, Divider, Tooltip, Toolbar, IconButton, Space } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { ExportButton } from '../../../../modules/config/components/export-button'
import { useStyles } from './config-toolbar.styles'

export interface ConfigToolbarProps {
  configName: string
  isWriteable: boolean
  isLoading: boolean
  isSaving: boolean
  isDirty: boolean
  onSave: () => void
  onRefresh: () => void
  onDelete: () => void
  /** Whether the configuration can be deleted (delete permission AND writeable). When false the delete button is hidden. Defaults to shown. */
  canDelete?: boolean
  /** Translation key for the disabled-save tooltip, so callers can distinguish "not writeable" from
   * "no update permission". Defaults to config_not_writeable. */
  saveDisabledTooltipKey?: string
  additionalButtons?: ReactNode[]
  leftAdditionalContent?: ReactNode
}

export function ConfigToolbar ({
  configName,
  isWriteable,
  isLoading,
  isSaving,
  isDirty,
  onSave,
  onRefresh,
  onDelete,
  canDelete,
  saveDisabledTooltipKey = 'config_not_writeable',
  additionalButtons = [],
  leftAdditionalContent
}: ConfigToolbarProps): React.JSX.Element {
  const { t } = useTranslation()
  const { styles } = useStyles()

  // Delete is offered (and enabled) only when the configuration can actually be deleted, i.e. the
  // user holds the delete permission AND the configuration is writeable. It is independent of the
  // update permission, so a read+delete user can still delete a writeable config.
  const showDelete = canDelete ?? true

  const saveButton = (
    <Button
      disabled={ !isDirty || !isWriteable }
      key="save"
      loading={ isSaving }
      onClick={ onSave }
      type="primary"
    >
      {t('save')}
    </Button>
  )

  const rightButtons: ReactNode[] = [
    ...additionalButtons,
    !isWriteable
      ? (
        <Tooltip
          key="save-tooltip"
          title={ t(saveDisabledTooltipKey) }
        >
          <span>{saveButton}</span>
        </Tooltip>
        )
      : saveButton
  ]

  return (
    <Toolbar>
      <Space size="extra-small">
        <Tooltip title={ t('refresh') }>
          <IconButton
            disabled={ isLoading }
            icon={ { value: 'refresh' } }
            onClick={ onRefresh }
          />
        </Tooltip>
        {showDelete && (
          <Tooltip title={ t('delete') }>
            <IconButton
              icon={ { value: 'trash' } }
              onClick={ onDelete }
            />
          </Tooltip>
        )}
        <ExportButton configName={ configName } />
        {leftAdditionalContent !== undefined && (
          <>
            <Divider
              className={ styles.divider }
              type="vertical"
            />
            {leftAdditionalContent}
          </>
        )}
      </Space>
      <ButtonGroup items={ rightButtons as any } />
    </Toolbar>
  )
}
