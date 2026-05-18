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
 * For advanced columns (type === ADVANCED_COLUMN_TYPE), `key` holds the user-defined title.
 */
export interface SchemaColumn {
  key: string
  fieldtype: string
  type: string
  /** Adapter-specific persisted column data (e.g. { advancedColumns, transformers } for advanced columns). */
  config?: Record<string, any>
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

/**
 * The special column key that identifies an advanced/pipeline column.
 * Advanced columns have a pipeline (sourceFields + transformers) instead of a direct field mapping.
 */
export const ADVANCED_COLUMN_KEY = 'advanced'

/**
 * The column type registered by the backend for advanced/pipeline columns.
 * Used as the reliable discriminator when loading persisted columns, because the key
 * is now set to the user-defined title rather than the hardcoded sentinel 'advanced'.
 */
export const ADVANCED_COLUMN_TYPE = 'dataobject.advanced'

/**
 * Frontend-only draft column type used by adapter column editors that support
 * advanced (pipeline) columns. Extends the base persisted shape with working-state
 * fields that are never written to the backend directly.
 */
export interface AdvancedEditorColumn {
  /** Frontend-only unique id for list keying. */
  _id: string
  key: string
  fieldtype: string
  type: string
  /** Persisted column config blob (e.g. { advancedColumns, transformers } for advanced columns). */
  config?: Record<string, any>
  /**
   * Frontend-only pipeline schema fetched from the available-columns API.
   * Drives what source field types are available in the pipeline form.
   * Never persisted.
   */
  pipelineConfig?: Record<string, any>
  /**
   * Draft pipeline value: { title, sourceFields, transformers }.
   * Present only for advanced columns (type === ADVANCED_COLUMN_TYPE).
   * Never persisted directly — on save, title becomes the column key and
   * sourceFields/transformers are packed into config.advancedColumns/config.transformers.
   */
  pipeline?: Record<string, any>
  /** Whether this column supports per-column locale selection. */
  localizable?: boolean
  locale?: string | null
}

/**
 * Converts a persisted SchemaColumn into an AdvancedEditorColumn draft.
 * For advanced columns (type === ADVANCED_COLUMN_TYPE) the persisted config is
 * unpacked into the frontend `pipeline` working state, and the column key
 * (which holds the user title) is mapped to pipeline.title.
 */
export const advancedFromSchemaColumn = (col: SchemaColumn): AdvancedEditorColumn => ({
  _id: crypto.randomUUID(),
  key: col.key,
  fieldtype: col.fieldtype,
  type: col.type,
  config: col.config,
  pipeline: col.type === ADVANCED_COLUMN_TYPE
    ? {
        // New format: key holds the title. Legacy format: key === 'advanced', title was a separate field.
        title: col.key !== ADVANCED_COLUMN_KEY ? col.key : (col as any).title,
        sourceFields: (col.config?.advancedColumns ?? []).map((sf: Record<string, any>) => ({
          ...sf,
          config: sf.config ?? {}
        })),
        transformers: col.config?.transformers
      }
    : undefined,
  locale: col.locale
})

/**
 * Converts an AdvancedEditorColumn draft back to a persisted SchemaColumn.
 * For advanced columns the frontend `pipeline` is packed into config.advancedColumns + config.transformers.
 */
export const advancedToSchemaColumn = (col: AdvancedEditorColumn): SchemaColumn => ({
  key: col.type === ADVANCED_COLUMN_TYPE && col.pipeline?.title !== undefined && col.pipeline.title !== ''
    ? col.pipeline.title as string
    : col.key,
  fieldtype: col.fieldtype,
  type: col.type,
  config: col.pipeline !== undefined
    ? {
        advancedColumns: (col.pipeline.sourceFields ?? []).map((sf: Record<string, any>) => ({
          ...sf,
          config: sf.config ?? {}
        })),
        transformers: col.pipeline.transformers
      }
    : col.config,
  locale: col.locale
})
