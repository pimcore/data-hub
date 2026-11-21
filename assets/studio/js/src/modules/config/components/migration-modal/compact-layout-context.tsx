/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { createContext, useContext, useMemo } from 'react'

export interface CompactLayoutContextProps {
  /**
   * True when the editor is rendered in a horizontally constrained context
   * (e.g. the migration modal's split view). Forms should collapse side-by-side
   * layouts into tabs when this is set.
   */
  compact: boolean
}

const CompactLayoutContext = createContext<CompactLayoutContextProps>({
  compact: false
})

export interface CompactLayoutProviderProps extends Partial<CompactLayoutContextProps> {
  children: React.ReactNode
}

export const CompactLayoutProvider = ({ children, compact = false }: CompactLayoutProviderProps): React.JSX.Element => {
  return useMemo(() => (
    <CompactLayoutContext.Provider value={ { compact } }>
      {children}
    </CompactLayoutContext.Provider>
  ), [children, compact])
}

export const useCompactLayout = (): CompactLayoutContextProps => {
  return useContext(CompactLayoutContext)
}
