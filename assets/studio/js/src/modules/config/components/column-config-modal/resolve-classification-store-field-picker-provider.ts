/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import type React from 'react'

export interface ClassificationStoreFieldPickerProviderProps {
  /** Class the picked classification store group/key relations are scoped to. */
  defaultClassId?: string
  children: React.ReactNode
}

export type ClassificationStoreFieldPickerProviderComponent = React.ComponentType<ClassificationStoreFieldPickerProviderProps>

/**
 * Resolves Studio's ClassificationStoreFieldPickerProvider from the SDK module at runtime. Studio exports it only
 * from the release that ships pimcore/studio-ui-bundle#4434 on, and this bundle runs against whatever Studio version
 * is installed, so a static named import would be `undefined` (and crash React) on older versions.
 */
export const resolveClassificationStoreFieldPickerProvider = (sdk: Record<string, unknown>): ClassificationStoreFieldPickerProviderComponent | undefined => {
  const provider = sdk.ClassificationStoreFieldPickerProvider

  return typeof provider === 'function' ? provider as ClassificationStoreFieldPickerProviderComponent : undefined
}
