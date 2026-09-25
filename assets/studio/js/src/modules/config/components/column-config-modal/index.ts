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

// The embeddable column editor (base editor, pipeline form, preview, locale control,
// fields panel, types) now lives in the Studio UI SDK — re-exported here so existing
// consumers of '@pimcore/data-hub' keep working unchanged.
export {
  ADVANCED_COLUMN_KEY,
  ADVANCED_COLUMN_TYPE,
  advancedFromSchemaColumn,
  advancedToSchemaColumn,
  BaseColumnEditor,
  ColumnEditorItemBody,
  ColumnLocaleControl,
  ColumnPipelineForm,
  ColumnPreview,
  useAddColumnGroups,
  type AdvancedEditorColumn,
  type BaseColumnEditorProps,
  type ColumnEditorHandle,
  type ColumnEditorItemBodyProps,
  type ColumnLocaleControlProps,
  type ColumnPipelineFormProps,
  type ColumnPreviewProps,
  type SchemaColumn
} from '@pimcore/studio-ui-bundle/modules/data-object'
