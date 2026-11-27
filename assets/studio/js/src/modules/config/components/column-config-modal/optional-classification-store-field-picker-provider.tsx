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
import * as studioElement from '@pimcore/studio-ui-bundle/modules/element'
import {
  resolveClassificationStoreFieldPickerProvider,
  type ClassificationStoreFieldPickerProviderProps
} from './resolve-classification-store-field-picker-provider'

const PickerProviderFromSdk = resolveClassificationStoreFieldPickerProvider(studioElement as Record<string, unknown>)

/**
 * Mounts the classification store field picker that Studio's grid source fields (Simple Field, Relation Field) use
 * to pick a group/key, when the installed Studio provides it. Otherwise renders the children unchanged: on those
 * versions the source fields either crash without it (fixed in Studio) or fall back to plain field selection.
 */
export const OptionalClassificationStoreFieldPickerProvider = ({ defaultClassId, children }: ClassificationStoreFieldPickerProviderProps): React.JSX.Element => {
  if (PickerProviderFromSdk === undefined) {
    return <>{ children }</>
  }

  return (
    <PickerProviderFromSdk defaultClassId={ defaultClassId }>
      { children }
    </PickerProviderFromSdk>
  )
}
