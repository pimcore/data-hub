/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import * as StudioComponents from '@pimcore/studio-ui-bundle/components'
import type React from 'react'

/**
 * TEMPORARY SEAM. `FormAnnotationsProvider` marks Form.Items without the form knowing, which
 * is exactly what a read-only review needs — but it is not in the pinned studio-ui canary, so
 * it is typed here and read off a namespace import at runtime. Delete this file and import
 * from `@pimcore/studio-ui-bundle/components` once the canary carries it.
 */

export type FormItemAnnotationStatus = 'added' | 'changed' | 'removed' | 'moved'

export interface FormItemAnnotation {
  status: FormItemAnnotationStatus
  /** rendered under the control, e.g. the value the field had before */
  hint?: React.ReactNode
}

/** keyed by the Form.Item's name, path segments joined by '.' */
export type FormAnnotations = Record<string, FormItemAnnotation>

interface FormAnnotationsProviderProps {
  annotations: FormAnnotations
  children?: React.ReactNode
}

type ProviderComponent = React.ComponentType<FormAnnotationsProviderProps>

const exported = (StudioComponents as unknown as Record<string, unknown>).FormAnnotationsProvider

/** null when the host SDK predates the annotation layer; the review then renders unmarked */
export const FormAnnotationsProvider: ProviderComponent | null =
  typeof exported === 'function' ? exported as ProviderComponent : null

export const supportsFormAnnotations = FormAnnotationsProvider !== null

type AnnotationHook = (name: Array<string | number> | string | undefined) => FormItemAnnotation | undefined

const exportedHook = (StudioComponents as unknown as Record<string, unknown>).useFormItemAnnotation

/**
 * The SDK's own per-item lookup, so a control the form does not bind through Form.Item — a
 * mapping row's header — can still read the mark the review put on it. A no-op where the
 * SDK has no annotation layer, so callers can use it unconditionally.
 */
export const useFormItemAnnotation: AnnotationHook =
  typeof exportedHook === 'function' ? exportedHook as AnnotationHook : () => undefined

/** the key the provider files an annotation under: the item's name path, dot-joined */
export const annotationKey = (name: Array<string | number>): string => name.map(String).join('.')
