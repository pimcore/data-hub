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
import { PreviewModeBar, Toolbar } from '@pimcore/studio-ui-bundle/components'
import { LanguageSelectionWithProvider } from '@pimcore/studio-ui-bundle/modules/data-object'

export interface PreviewTopBarProps {
  entity: string
}

/** The preview bar with the preview language select (DR-P7); rendered only while preview mode is on */
export const PreviewTopBar = ({ entity }: PreviewTopBarProps): React.JSX.Element => (
  <Toolbar
    align='center'
    position='content'
    theme='secondary'
  >
    <PreviewModeBar allowedClasses={ [entity] } />

    <LanguageSelectionWithProvider />
  </Toolbar>
)
