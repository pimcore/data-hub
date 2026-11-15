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
import type { ChangeSetReviewData, ReviewSlotData } from '@pimcore/change-control-bundle/sdk'
import { type BackendConfiguration } from '../../src/modules/graphql/components/backend-types'
import { configChanges, proposedConfiguration } from '../../src/modules/graphql/change-control/config-review-model'
import { toEditorShape, toStoredShape } from '../../src/modules/graphql/change-control/document-shape'
import { transformBackendToForm } from '../../src/modules/graphql/utils/transformers'

// Change Control keeps a key a change set removes as a null leaf: an agent drops an entity by sending it as null

type Leaves = Record<string, unknown>

const slot = (current: Leaves | null, proposed: Leaves | null, base: Leaves | null = current): ReviewSlotData =>
  ({ shape: 'record', current, proposed, base })

const review = (slots: Record<string, ReviewSlotData>): ChangeSetReviewData =>
  ({ slots }) as unknown as ChangeSetReviewData

const CAR = { 'schema.queryEntities.Car.id': 'Car', 'schema.queryEntities.Car.name': 'Car' }
const PRODUCT = { 'schema.queryEntities.Product.id': 'Product', 'schema.queryEntities.Product.name': 'Product' }

const live = {
  general: { name: 'shop' },
  schema: {
    queryEntities: [{ id: 'Car', name: 'Car' }, { id: 'Product', name: 'Product' }],
    specialEntities: [{ name: 'asset', readAllowed: true, createAllowed: false, updateAllowed: false, deleteAllowed: false }]
  }
} as unknown as BackendConfiguration

describe('a null marker in the review', () => {
  it('names a schema entity it removes once, as removed', () => {
    const changes = configChanges(review({
      schema: slot({ ...CAR, ...PRODUCT }, { ...PRODUCT, 'schema.queryEntities.Car': null })
    }))

    expect(changes).toEqual([expect.objectContaining({
      address: 'schema.queryEntities.Car', label: 'Car', status: 'removed', formPath: 'schema.query'
    })])
  })

  it('reads a field set to null as removed, and one null on both sides as unchanged', () => {
    const changes = configChanges(review({
      general: slot({ 'general.group': 'shop', 'general.description': null }, { 'general.group': null, 'general.description': null })
    }))

    expect(changes).toEqual([expect.objectContaining({
      address: 'general.group', status: 'removed', current: 'shop', proposed: undefined
    })])
  })

  it('lists nothing for a key the compared side never had', () => {
    expect(configChanges(review({ schema: slot(PRODUCT, { ...PRODUCT, 'schema.queryEntities.Car': null }) }))).toEqual([])
  })

  it('gives the editor a query entity list without the entity it removes', () => {
    const configuration = proposedConfiguration(review({
      schema: slot({ ...CAR, ...PRODUCT }, { ...PRODUCT, 'schema.queryEntities.Car': null })
    }), live)

    expect(transformBackendToForm(configuration, 'shop').schema.query).toEqual([
      expect.objectContaining({ id: 'Product', entity: 'Product' })
    ])
  })

  it('drops a mutation entity and a generic type as well, and the editor reads the result', () => {
    const configuration = proposedConfiguration(review({
      schema: slot(
        { ...CAR, ...PRODUCT, 'schema.specialEntities.asset.read': true },
        { ...PRODUCT, 'schema.queryEntities.Car': null, 'schema.mutationEntities.Car': null, 'schema.specialEntities.asset': null }
      )
    }), live)

    expect(configuration.schema?.queryEntities).toEqual({ Product: { id: 'Product', name: 'Product' } })
    expect(configuration.schema).not.toHaveProperty('mutationEntities')
    expect(configuration.schema).not.toHaveProperty('specialEntities')
    const form = transformBackendToForm(configuration, 'shop')
    expect(form.schema.query).toEqual([expect.objectContaining({ id: 'Product', entity: 'Product' })])
    expect(form.schema.mutation).toEqual([])
    expect(form.schema.genericTypes).toEqual([])
  })
})

describe('document shape', () => {
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

  it('leaves out a null generic type and null schema entities when shaping for the editor', () => {
    const doc = toEditorShape({
      schema: {
        queryEntities: { Car: null, Product: { id: 'Product', name: 'Product' } },
        mutationEntities: { Car: null },
        specialEntities: { asset: null, document: { read: true } }
      }
    })

    expect(doc.schema.queryEntities).toEqual({ Product: { id: 'Product', name: 'Product' } })
    expect(doc.schema.mutationEntities).toEqual({})
    expect(doc.schema.specialEntities).toEqual([expect.objectContaining({ name: 'document', readAllowed: true, createAllowed: false })])
    expect(() => transformBackendToForm(doc, 'shop')).not.toThrow()
  })
})
