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
import {
  ColumnEditorItemBody as SdkColumnEditorItemBody,
  type ColumnEditorItemBodyProps
} from '@pimcore/studio-ui-bundle/modules/data-object'
import { useCompactLayout } from '../migration-modal'

export type { ColumnEditorItemBodyProps }

/**
 * Thin wrapper around the Studio SDK's ColumnEditorItemBody that resolves `compact` from
 * CompactLayoutContext when the caller doesn't pass it explicitly - the same BC bridge
 * `base-column-editor.tsx` provides for BaseColumnEditor and `column-pipeline-form.tsx` provides
 * for ColumnPipelineForm. Kept for direct consumers of '@pimcore/data-hub' that render this
 * component outside BaseColumnEditor.
 */
export const ColumnEditorItemBody = (props: ColumnEditorItemBodyProps): React.JSX.Element => {
  const { compact: compactFromContext } = useCompactLayout()

  return (
    <SdkColumnEditorItemBody
      { ...props }
      compact={ props.compact ?? compactFromContext }
    />
  )
}
