/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { useEffect, useRef } from 'react'
import { useUser } from '@pimcore/studio-ui-bundle/modules/auth'
import { resolvePreviewLanguage } from './preview-language'
import { Flex, PreviewModeBar, PreviewModeSwitch, Toolbar, usePreviewModeOptional } from '@pimcore/studio-ui-bundle/components'

export interface PreviewHeaderProps {
  entity: string
  /** The persisted preview language: the bar starts with it */
  language?: string
  onLanguageChange?: (language: string) => void
}

/** The row below the modal title, in the same Toolbar as the Grid Configuration modal's (its border): the switch and the bar: the language switch always (the saved language), the item picker while preview mode is on; laid out as the grid modal's row, the flag at the right end (DR-P11, F24) */
export const PreviewHeader = ({ entity, language, onLanguageChange }: PreviewHeaderProps): React.JSX.Element => {
  const { enabled, language: previewLanguage, setLanguage } = usePreviewModeOptional()

  const user = useUser()
  // The language the header put into the preview state itself: not the user's choice, so it is not saved
  const seeded = useRef<string | undefined>(undefined)

  useEffect(() => {
    if (previewLanguage !== undefined) return
    const resolved = resolvePreviewLanguage(language, user.contentLanguages as string[] | undefined)
    seeded.current = resolved
    setLanguage(resolved)
  }, [language, previewLanguage])

  useEffect(() => {
    if (previewLanguage === undefined) return
    if (previewLanguage === seeded.current) return
    seeded.current = undefined
    if (previewLanguage !== language) onLanguageChange?.(previewLanguage)
  }, [previewLanguage])

  return (
    <Toolbar
      padding={ { x: 'none' } }
      position='content'
      theme='secondary'
    >
      <Flex
        align='center'
        data-testid='preview-mode-header'
        gap='small'
        style={ { width: '100%', minWidth: 0 } }
      >
        <PreviewModeSwitch />

        <PreviewModeBar
          allowedClasses={ [entity] }
          showItem={ enabled }
        />
      </Flex>
    </Toolbar>
  )
}
