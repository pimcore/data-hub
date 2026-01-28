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
import { ModalUpload, IconButton, Tooltip } from '@pimcore/studio-ui-bundle/components'
import { getPrefix } from '@pimcore/studio-ui-bundle/api'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'

interface ImportButtonProps {
  onSuccess: () => Promise<void>
  disabled?: boolean
}

export const ImportButton = ({
  onSuccess,
  disabled = false
}: ImportButtonProps): React.JSX.Element => {
  const { t } = useTranslation()

  return (
    <ModalUpload
      accept=".json,application/json"
      action={ `${getPrefix()}/bundle/data-hub/config/import` }
      maxItems={ 1 }
      multiple={ false }
      name="file"
      onSuccess={ onSuccess }
    >
      <Tooltip title={ t('tree.actions.import') }>
        <IconButton
          disabled={ disabled }
          icon={ { value: 'import-csv' } }
          type="link"
        />
      </Tooltip>
    </ModalUpload>
  )
}
