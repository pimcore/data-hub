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
import { Button, Space } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from 'react-i18next'

export interface ColumnEditorActionsProps {
  /**
   * False while this class's available columns are still loading, so the draft's field definition
   * types are unresolved. Apply is refused then — see `isHydrated` in use-column-editor-state.
   */
  isHydrated: boolean
  onDiscard: () => void
  onApply: () => void
}

/**
 * The editor toolbar's Discard and Apply.
 *
 * Its own file because base-column-editor.tsx is capped at 300 lines and preview mode fills it.
 */
export const ColumnEditorActions = ({
  isHydrated,
  onDiscard,
  onApply
}: ColumnEditorActionsProps): React.JSX.Element => {
  const { t } = useTranslation()

  return (
    <Space size='extra-small'>
      <Button
        onClick={ onDiscard }
        type='default'
      >
        { t('data-hub.column-config-modal.discard') }
      </Button>

      <Button
        disabled={ !isHydrated }
        onClick={ onApply }
        type='primary'
      >
        { t('data-hub.column-config-modal.apply') }
      </Button>
    </Space>
  )
}
