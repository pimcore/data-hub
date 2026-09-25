/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { forwardRef } from 'react'
import {
  BaseColumnEditor as SdkBaseColumnEditor,
  type BaseColumnEditorProps,
  type ColumnEditorHandle
} from '@pimcore/studio-ui-bundle/modules/data-object'
import { useCompactLayout } from '../migration-modal'

export type { BaseColumnEditorProps }

/**
 * Thin wrapper around the Studio SDK's BaseColumnEditor that resolves `compact` from
 * CompactLayoutContext when the caller doesn't pass it explicitly.
 *
 * The four adapter bundles construct <BaseColumnEditor> themselves and predate the `compact`
 * prop the editor took over from CompactLayoutContext, so without this wrapper the migration
 * split view's compact pipeline-form layout regresses to the normal layout for every adapter.
 * MigrationModal still wraps its split-view children in CompactLayoutProvider; this wrapper
 * reads that context once (defaulting to false outside the provider) and forwards it as the
 * explicit prop the SDK editor now expects, so adapters need no change.
 */
export const BaseColumnEditor = forwardRef<ColumnEditorHandle, BaseColumnEditorProps>(
  function BaseColumnEditor (props, ref) {
    const { compact: compactFromContext } = useCompactLayout()

    return (
      <SdkBaseColumnEditor
        { ...props }
        compact={ props.compact ?? compactFromContext }
        ref={ ref }
      />
    )
  }
)
