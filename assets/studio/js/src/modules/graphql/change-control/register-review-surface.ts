/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { container } from '@pimcore/studio-ui-bundle'
import type {
  ReviewSurfaceRegistry,
  REVIEW_SURFACE_REGISTRY_ID as CcReviewSurfaceRegistryId
} from '@pimcore/change-control-bundle/sdk'
import { GraphQLConfigReviewSurface } from './graphql-config-review-surface'

// the literal is pinned to Change Control's own contract, so a rename there fails the build
// here; the value cannot be imported, because that would make an optional bundle a hard
// dependency of this one
const REVIEW_SURFACE_REGISTRY_ID: typeof CcReviewSurfaceRegistryId = 'ChangeControl/Review/SurfaceRegistry'

/** the subject type GraphQlConfigPolicy records against */
const SUBJECT_TYPE = 'data-hub-graphql-config'

/**
 * Registers the bundle's review surface, if Change Control is installed at all. Resolving an
 * unbound id throws, which is the only way to ask — and a missing review lane is a perfectly
 * normal installation, not an error.
 */
export function registerChangeControlReviewSurface (): void {
  // every module's onInit runs synchronously, so a macrotask is after all of them
  setTimeout(() => {
    let registry: ReviewSurfaceRegistry | undefined
    try {
      registry = container.get<ReviewSurfaceRegistry>(REVIEW_SURFACE_REGISTRY_ID)
    } catch {
      return
    }

    registry?.registerDynamicType({ id: SUBJECT_TYPE, component: GraphQLConfigReviewSurface, layout: { width: 1440 } })
  }, 0)
}
