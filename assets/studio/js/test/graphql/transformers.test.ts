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

describe('transformSchemaEntitiesFromBackend', () => {
  // a proposal that removes one entity carries it as null, and the review rendered it (B61)
  it('leaves out an entity a proposal removes with null, and keeps the others', () => {
    const entities = { Car: { id: 'Car', name: 'Car' }, Manufacturer: null } as unknown as Parameters<typeof transformSchemaEntitiesFromBackend>[0]

    expect(transformSchemaEntitiesFromBackend(entities)).toEqual([{ id: 'Car', name: 'Car', entity: 'Car' }])
  })

  it('leaves out a null in a list as well', () => {
    const entities = [{ id: 'Car', name: 'Car' }, null] as unknown as Parameters<typeof transformSchemaEntitiesFromBackend>[0]

    expect(transformSchemaEntitiesFromBackend(entities)).toEqual([{ id: 'Car', name: 'Car', entity: 'Car' }])
  })
})
