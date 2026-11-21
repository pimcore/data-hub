/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import {
  SidebarContext,
  type SidebarContextValue
} from '@pimcore/studio-ui-bundle/components'
import React, { useMemo, useState } from 'react'

export interface SidebarProviderProps {
  children: React.ReactNode
}

/**
 * Minimal SidebarContext provider that opens the 'filter' tab by default.
 * SidebarProvider from the SDK is not exported, so this is an inline workaround.
 */
export const SidebarProvider = ({ children }: SidebarProviderProps): React.JSX.Element => {
  const [activeTab, setActiveTab] = useState<string>('filter')

  const contextValue = useMemo<SidebarContextValue>(() => ({
    entries: [],
    buttons: [],
    sizing: 'default',
    highlights: [],
    activeTab,
    setEntries: () => {},
    setButtons: () => {},
    setSizing: () => {},
    setHighlights: () => {},
    setActiveTab,
    addEntry: () => {},
    removeEntry: () => {},
    addButton: () => {},
    removeButton: () => {},
    toggleHighlight: () => {},
    openTab: (key: string) => { setActiveTab(key) },
    closeTab: () => { setActiveTab('') },
    toggleTab: (key: string) => {
      setActiveTab((prev) => prev === key ? '' : key)
    }
  }), [activeTab])

  return (
    <SidebarContext.Provider value={ contextValue }>
      {children}
    </SidebarContext.Provider>
  )
}
