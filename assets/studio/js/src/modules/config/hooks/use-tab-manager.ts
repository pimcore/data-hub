/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { useState, useCallback } from 'react'
import { type BundleDataHubConfiguration } from '../config-api-slice.gen'

interface UseTabManagerReturn {
  openedConfigs: BundleDataHubConfiguration[]
  activeTabKey: string | undefined
  handleOpenConfig: (config: BundleDataHubConfiguration) => void
  handleCloseTab: (key: string) => void
  handleChangeTab: (key: string) => void
}

export const useTabManager = (): UseTabManagerReturn => {
  const [openedConfigs, setOpenedConfigs] = useState<BundleDataHubConfiguration[]>([])
  const [activeTabKey, setActiveTabKey] = useState<string | undefined>(undefined)

  const handleOpenConfig = useCallback((config: BundleDataHubConfiguration): void => {
    const isAlreadyOpened = openedConfigs.some(item => item.id === config.id)

    if (!isAlreadyOpened) {
      setOpenedConfigs(prev => [...prev, config])
    }

    setActiveTabKey(config.id)
  }, [openedConfigs])

  const handleCloseTab = useCallback((key: string): void => {
    setOpenedConfigs(prev => {
      const targetIndex = prev.findIndex((tab) => tab?.id === key)
      const updatedConfigs = prev.filter((config) => config.id !== key)

      if (key === activeTabKey) {
        const prevTab = prev[targetIndex - 1]
        const nextTab = prev[targetIndex + 1]

        setActiveTabKey(prevTab?.id ?? nextTab?.id)
      }

      return updatedConfigs
    })
  }, [activeTabKey])

  const handleChangeTab = useCallback((key: string): void => {
    setActiveTabKey(key)
  }, [])

  return {
    openedConfigs,
    activeTabKey,
    handleOpenConfig,
    handleCloseTab,
    handleChangeTab
  }
}
