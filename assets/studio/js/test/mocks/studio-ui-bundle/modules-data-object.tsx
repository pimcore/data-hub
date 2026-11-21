/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { createContext, useContext } from 'react'

export const LanguageSelectionContext = createContext<{ currentLanguage: string } & Record<string, any>>({ currentLanguage: 'en' })

export const useLanguageSelection = (): { currentLanguage: string } => useContext(LanguageSelectionContext)

export const LanguageSelectionWithProvider = (): React.JSX.Element => {
  const { currentLanguage } = useContext(LanguageSelectionContext)
  return <select aria-label='preview-language'><option>{ currentLanguage }</option></select>
}

export const useClassDefinitions = (): { getByName: (name: string) => { id: string } | undefined } => ({
  getByName: (name: string) => ({ id: `class-${name}` })
})
