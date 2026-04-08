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

/**
 * The special column key that identifies an advanced/pipeline column.
 * Advanced columns have a pipeline (sourceFields + transformers) instead of a direct field mapping.
 */
export const ADVANCED_COLUMN_KEY = 'advanced'

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
   * Present only for advanced columns (key === ADVANCED_COLUMN_KEY).
   * Never persisted directly — serialised into config.advancedColumns + config.transformers on save.
   */
  pipeline?: Record<string, any>
  /** Whether this column supports per-column locale selection. */
  localizable?: boolean
  locale?: string | null
}

/**
 * Converts a persisted SchemaColumn into an AdvancedEditorColumn draft.
 * For advanced columns (key === ADVANCED_COLUMN_KEY) the persisted config is
 * unpacked into the frontend `pipeline` working state.
 */
export const advancedFromSchemaColumn = (col: SchemaColumn): AdvancedEditorColumn => ({
  _id: crypto.randomUUID(),
  key: col.key,
  fieldtype: col.fieldtype,
  type: col.type,
  config: col.config,
  pipeline: col.key === ADVANCED_COLUMN_KEY
    ? {
        title: col.title,
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
  key: col.key,
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
  title: col.pipeline?.title,
  locale: col.locale
})
