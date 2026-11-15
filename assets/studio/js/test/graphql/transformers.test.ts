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
import { transformSchemaEntitiesFromBackend } from '../../src/modules/graphql/utils/transformers'
import { toEditorShape } from '../../src/modules/graphql/change-control/document-shape'

describe('transformSchemaEntitiesFromBackend', () => {
  // a proposal that removes one entity carries it as null
  it('leaves out an entity a proposal removes with null, and keeps the others', () => {
    expect(transformSchemaEntitiesFromBackend({ Car: { id: 'Car', name: 'Car' }, Manufacturer: null }))
      .toEqual([{ id: 'Car', name: 'Car', entity: 'Car' }])
  })

  it('leaves out a null in a list as well', () => {
    expect(transformSchemaEntitiesFromBackend([{ id: 'Car', name: 'Car' }, null]))
      .toEqual([{ id: 'Car', name: 'Car', entity: 'Car' }])
  })
})

describe('toEditorShape', () => {
  it('leaves out a generic type a proposal removes with null, and keeps the others', () => {
    const shaped = toEditorShape({ schema: { specialEntities: { document: null, asset: { read: true } } } })

    expect(shaped.schema.specialEntities).toEqual([{
      read: true, name: 'asset', readAllowed: true, createAllowed: false, updateAllowed: false, deleteAllowed: false
    }])
  })
})
