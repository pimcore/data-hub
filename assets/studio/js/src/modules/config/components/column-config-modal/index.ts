/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

export * from './column-config-modal'

// BaseColumnEditor, ColumnPipelineForm, and ColumnEditorItemBody are local thin wrappers (see
// base-column-editor.tsx, column-pipeline-form.tsx, column-editor-item.tsx) that resolve
// `compact` from CompactLayoutContext for callers that don't pass it explicitly. Before the
// column editor moved into the Studio UI SDK these three read that context internally; the
// wrappers keep that behavior for existing '@pimcore/data-hub' consumers built against the
// pre-move, context-only API.
export * from './base-column-editor'
export * from './column-pipeline-form'
export * from './column-editor-item'

// The rest of the embeddable column editor (preview, locale control, fields panel, types) now
// lives in the Studio UI SDK — re-exported here so existing consumers of '@pimcore/data-hub'
// keep working unchanged.
export {
  ADVANCED_COLUMN_KEY,
  ADVANCED_COLUMN_TYPE,
  advancedFromSchemaColumn,
  advancedToSchemaColumn,
  ColumnLocaleControl,
  ColumnPreview,
  useAddColumnGroups,
  type AdvancedEditorColumn,
  type ColumnEditorHandle,
  type ColumnLocaleControlProps,
  type ColumnPreviewProps,
  type SchemaColumn
} from '@pimcore/studio-ui-bundle/modules/data-object'
