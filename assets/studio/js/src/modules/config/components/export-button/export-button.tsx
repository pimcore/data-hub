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
import { IconButton, Tooltip } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { getExportUrl } from '../../utils/get-export-url'

interface ExportButtonProps {
  configName: string
  disabled?: boolean
}

export const ExportButton = ({ configName, disabled }: ExportButtonProps): React.JSX.Element => {
  const { t } = useTranslation()

  const handleExport = (): void => {
    window.location.href = getExportUrl(configName)
  }

  return (
    <Tooltip title={ t('tree.actions.export') }>
      <IconButton
        disabled={ disabled }
        icon={ { value: 'export' } }
        onClick={ handleExport }
      />
    </Tooltip>
  )
}
