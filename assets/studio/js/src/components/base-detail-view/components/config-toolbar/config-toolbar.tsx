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
  additionalButtons = [],
  leftAdditionalContent
}: ConfigToolbarProps): React.JSX.Element {
  const { t } = useTranslation()
  const { styles } = useStyles()

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
          title={ t('config_not_writeable') }
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
        <Tooltip title={ isWriteable ? t('delete') : t('config_not_writeable') }>
          <IconButton
            disabled={ !isWriteable }
            icon={ { value: 'trash' } }
            onClick={ onDelete }
          />
        </Tooltip>
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
