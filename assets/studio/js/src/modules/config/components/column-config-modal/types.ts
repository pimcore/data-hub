/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { type GridColumnConfiguration } from '@pimcore/studio-ui-bundle/api/data-object'

/**
 * Persisted column shape shared across all Data Hub adapter bundles.
 */
export interface SchemaColumn {
  key: string
  fieldtype: string
  type: string
  /** Adapter-specific persisted column data (e.g. { advancedColumns, transformers } for simple-rest). */
  config?: Record<string, any>
  title?: string
  locale?: string | null
}

/**
 * Imperative handle exposed by adapter-specific column editor components via forwardRef.
 * Allows ColumnConfigModal to imperatively read the current draft and add a column.
 */
export interface ColumnEditorHandle<TColumns = SchemaColumn> {
  /** Returns the current list of columns from the editor draft. */
  getColumns: () => TColumns[]
  /** Programmatically adds a column from the available-columns list. */
  addColumn: (column: GridColumnConfiguration) => void
}
