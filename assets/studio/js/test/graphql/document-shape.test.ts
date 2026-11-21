/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { describe, expect, it } from 'vitest'
import { type BackendConfiguration } from '../../src/modules/graphql/components/backend-types'
import { proposedConfiguration } from '../../src/modules/graphql/change-control/config-review-model'
import { toStoredShape } from '../../src/modules/graphql/change-control/document-shape'
import { transformBackendToForm } from '../../src/modules/graphql/utils/transformers'

describe('toStoredShape', () => {
  it('skips a null entry of the live lists instead of failing on it', () => {
    const stored = toStoredShape({
      schema: {
        queryEntities: [null, { id: 'Car', name: 'Car' }],
        mutationEntities: [{ name: 'Product' }, null],
        specialEntities: [null, { name: 'asset', readAllowed: true }]
      }
    })

    expect(stored.schema).toEqual({
      queryEntities: { Car: { id: 'Car', name: 'Car' } },
      mutationEntities: { Product: { name: 'Product' } },
      specialEntities: { asset: { name: 'asset', read: true, create: false, update: false, delete: false } }
    })
  })

  it('lets the review build the editor configuration from a live document with a null entry', () => {
    const live = {
      general: { name: 'shop' },
      schema: {
        queryEntities: [null, { id: 'Car', name: 'Car' }],
        specialEntities: [{ name: 'asset', readAllowed: true }, null]
      }
    } as unknown as BackendConfiguration

    const form = transformBackendToForm(proposedConfiguration(undefined, live), 'shop')

    expect(form.schema.query).toEqual([expect.objectContaining({ id: 'Car', entity: 'Car' })])
    expect(form.schema.genericTypes).toEqual([expect.objectContaining({ name: 'asset', read: true })])
  })
})
