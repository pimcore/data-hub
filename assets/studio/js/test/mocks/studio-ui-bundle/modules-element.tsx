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

export const TabId = { Collection: 'collection', Group: 'group', GroupByKey: 'group-by-key' }

export const ClassificationStoreModalProvider = ({ children }: { children?: React.ReactNode }): React.JSX.Element => <>{ children }</>
export const openClassificationStoreModal = jest.fn()

// The real provider keeps one update callback; the last registered one is what the modal reports to
let onUpdateCallback: (data: { data?: unknown[] }) => void = () => {}
/** Stands in for the modal's "Apply selection": hands the rows back to whoever opened it */
export const applyClassificationStoreSelection = (rows: unknown[]): void => { onUpdateCallback({ data: rows }) }

export const useClassificationStoreModal = (
  { onUpdate }: { onUpdate?: (data: { data?: unknown[] }) => void }
): { openModal: (context: unknown) => void, closeModal: () => void } => {
  if (onUpdate !== undefined) onUpdateCallback = onUpdate
  return { openModal: openClassificationStoreModal, closeModal: () => {} }
}
