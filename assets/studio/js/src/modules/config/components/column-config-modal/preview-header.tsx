/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { useEffect } from 'react'
import { Divider } from 'antd'
import { Flex, PreviewModeBar, PreviewModeSwitch, usePreviewMode } from '@pimcore/studio-ui-bundle/components'

export interface PreviewHeaderProps {
  entity: string
  /** The persisted preview language: the bar starts with it */
  language?: string
  onLanguageChange?: (language: string) => void
}

/** The row below the modal title: the switch and, while preview mode is on, the bar with the language switch, then a divider (DR-P7) */
export const PreviewHeader = ({ entity, language, onLanguageChange }: PreviewHeaderProps): React.JSX.Element => {
  const { enabled, language: previewLanguage, setLanguage } = usePreviewMode()

  useEffect(() => {
    if (language !== undefined && previewLanguage === undefined) setLanguage(language)
  }, [language, previewLanguage])

  useEffect(() => {
    if (previewLanguage !== undefined && previewLanguage !== language) onLanguageChange?.(previewLanguage)
  }, [previewLanguage])

  return (
    <>
      <Flex
        align='center'
        data-testid='preview-mode-header'
        gap='small'
      >
        <PreviewModeSwitch />

        { enabled && (
          <div style={ { flex: 1, minWidth: 0 } }>
            <PreviewModeBar allowedClasses={ [entity] } />
          </div>
        ) }
      </Flex>

      { enabled && (
        <Divider
          data-testid='preview-mode-header-divider'
          style={ { margin: '8px 0' } }
        />
      ) }
    </>
  )
}
