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

export const SelectionType = { Single: 'single' }
export const useElementSelector = (): { open: () => void } => ({ open: () => {} })

export const ClassificationStoreFieldPickerProvider = ({ children }: { children?: React.ReactNode }): React.JSX.Element => <>{ children }</>
export const openClassificationStorePicker = jest.fn()
export const useClassificationStoreFieldPicker = (): { openPicker: (args: unknown) => void } => ({ openPicker: openClassificationStorePicker })
