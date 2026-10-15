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
import { configBrief } from '../../src/modules/graphql/change-control/config-brief'

/** echoes the key and its count, so a test reads which string was picked */
const t = (key: string, options?: Record<string, unknown>): string =>
  options?.count === undefined ? key : `${key}:${String(options.count)}`

const entity = (id: string): { id: string, name: string } => ({ id, name: id })

describe('configBrief', () => {
  it('names up to two query entities and counts the mutations', () => {
    const brief = configBrief({
      general: { name: 'shop', active: true, description: 'Shop API', group: 'Shop' },
      schema: {
        queryEntities: { Car: entity('Car'), Product: entity('Product') },
        mutationEntities: [entity('Car')]
      },
      workspaces: { object: [{ cpath: '/', read: true, create: false, update: false, delete: false }] }
    } as unknown as BackendConfiguration, t)

    expect(brief).toEqual({
      name: 'shop',
      active: true,
      description: 'Shop API',
      sections: [
        { key: 'general', label: 'data-hub.tabs.general', value: 'Group Shop' },
        {
          key: 'schema',
          label: 'data-hub.tabs.schema-definition',
          value: 'Car, Product',
          note: 'data-hub.review.outline.mutations-one:1'
        },
        { key: 'security', label: 'data-hub.tabs.security-definition', value: 'data-hub.review.outline.rules-one:1' }
      ]
    })
  })

  it('counts more than two query entities and the rules of every workspace kind', () => {
    const rule = { cpath: '/', read: true, create: false, update: false, delete: false }
    const brief = configBrief({
      general: { name: 'shop', active: false, description: '', group: '' },
      schema: { queryEntities: [entity('Car'), entity('Product'), entity('Bike')] },
      workspaces: { document: [rule], asset: [rule], object: [rule] }
    } as unknown as BackendConfiguration, t)

    expect(brief.active).toBe(false)
    expect(brief.description).toBeUndefined()
    expect(brief.sections).toEqual([
      { key: 'schema', label: 'data-hub.tabs.schema-definition', value: 'data-hub.review.outline.queries:3', note: undefined },
      { key: 'security', label: 'data-hub.tabs.security-definition', value: 'data-hub.review.outline.rules:3' }
    ])
  })

  it('says out loud that an endpoint without a workspace reads nothing', () => {
    const brief = configBrief({} as unknown as BackendConfiguration, t)

    expect(brief).toEqual({
      name: '',
      active: false,
      description: undefined,
      sections: [{ key: 'security', label: 'data-hub.tabs.security-definition', value: 'data-hub.review.outline.nothing' }]
    })
  })
})
