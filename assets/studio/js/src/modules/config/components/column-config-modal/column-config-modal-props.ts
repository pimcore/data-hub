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
import { type ColumnEditorHandle, type SchemaColumn } from './types'

/**
 * Props passed from ColumnConfigModal down to the consumer's renderEditor callback.
 * The consumer must forward `ref` to their editor component so ColumnConfigModal
 * can imperatively call `getColumns()` on confirm.
 */
export interface EditorRenderProps<TColumns = SchemaColumn> {
  ref: React.Ref<ColumnEditorHandle<TColumns>>
  classDefinitionId: string
  columns: TColumns[]
  entity: string
  /** True in the split migration view — the editor should hide its own Apply/Discard toolbar. */
  hideToolbar: boolean
  /** When true, only columns marked as exportable are offered in the add-column dropdown. */
  exportableOnly: boolean
  onApply: (columns: TColumns[]) => void
  onCancel: () => void
  /** The currently persisted preview language for this entity. */
  language?: string
  /** Called when the user changes the preview language so the adapter can persist it. */
  onLanguageChange?: (language: string) => void
  /** View only: the columns can be looked at, not changed. */
  readOnly?: boolean
  /** View only: the columns before the change under review; the editor marks what differs. */
  previousColumns?: TColumns[]
}

export interface ColumnConfigModalProps<TColumns = SchemaColumn> {
  entity: string
  /**
   * The numeric Pimcore class definition ID (e.g. "EV", "42").
   * When omitted the ID is resolved from the class name via useClassDefinitions().
   */
  classDefinitionId?: string
  columns: TColumns[]
  /**
   * Legacy ExtJS column configuration blob. When present, the migration flow is shown.
   * When absent (undefined), the normal new-format editor modal is shown.
   */
  columnConfig?: Record<string, unknown>
  open: boolean
  /** Already-translated modal title string. */
  title: string
  onApply: (columns: TColumns[]) => void
  onCancel: () => void
  /** The currently persisted preview language for this entity. */
  language?: string
  /** Called when the user changes the preview language so the adapter can persist it. */
  onLanguageChange?: (language: string) => void
  /** When true, only columns marked as exportable are offered in the add-column dropdown. */
  exportableOnly?: boolean
  /** View only, e.g. in a review: the columns can be looked at, not changed. */
  readOnly?: boolean
  /** With `readOnly`: the columns before the change under review; added, changed and removed ones are marked. */
  previousColumns?: TColumns[]
  /**
   * Render prop that returns the adapter-specific column editor element.
   * The consumer MUST forward the `ref` to their editor component (forwardRef).
   *
   * @example
   * renderEditor={(props) => <ColumnEditor {...props} />}
   */
  renderEditor: (props: EditorRenderProps<TColumns>) => React.ReactNode
}
