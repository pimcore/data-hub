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

// BaseColumnEditor is a local thin wrapper (see base-column-editor.tsx) that resolves
// `compact` from CompactLayoutContext for adapters that don't pass it explicitly.
export * from './base-column-editor'

// The rest of the embeddable column editor (pipeline form, preview, locale control, fields
// panel, types) now lives in the Studio UI SDK — re-exported here so existing consumers of
// '@pimcore/data-hub' keep working unchanged.
export {
  ADVANCED_COLUMN_KEY,
  ADVANCED_COLUMN_TYPE,
  advancedFromSchemaColumn,
  advancedToSchemaColumn,
  ColumnEditorItemBody,
  ColumnLocaleControl,
  ColumnPipelineForm,
  ColumnPreview,
  useAddColumnGroups,
  type AdvancedEditorColumn,
  type ColumnEditorHandle,
  type ColumnEditorItemBodyProps,
  type ColumnLocaleControlProps,
  type ColumnPipelineFormProps,
  type ColumnPreviewProps,
  type SchemaColumn
} from '@pimcore/studio-ui-bundle/modules/data-object'
