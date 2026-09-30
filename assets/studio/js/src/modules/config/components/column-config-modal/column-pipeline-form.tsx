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
  ColumnPipelineForm as SdkColumnPipelineForm,
  type ColumnPipelineFormProps
} from '@pimcore/studio-ui-bundle/modules/data-object'
import { useCompactLayout } from '../migration-modal'

export type { ColumnPipelineFormProps }

/**
 * Thin wrapper around the Studio SDK's ColumnPipelineForm that resolves `compact` from
 * CompactLayoutContext when the caller doesn't pass it explicitly - the same BC bridge
 * `base-column-editor.tsx` provides for BaseColumnEditor.
 *
 * Before the column editor moved into the Studio UI SDK, this component read
 * CompactLayoutContext internally and needed no `compact` prop at all. Any direct consumer of
 * '@pimcore/data-hub' that renders ColumnPipelineForm outside BaseColumnEditor (instead of
 * through the wrapper above) predates the explicit `compact` prop the SDK form took over from
 * that context, so this wrapper keeps resolving it the same way.
 */
export const ColumnPipelineForm = (props: ColumnPipelineFormProps): React.JSX.Element => {
  const { compact: compactFromContext } = useCompactLayout()

  return (
    <SdkColumnPipelineForm
      { ...props }
      compact={ props.compact ?? compactFromContext }
    />
  )
}
