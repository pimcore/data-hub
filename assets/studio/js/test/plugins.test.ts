/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { describe, expect, it, vi } from 'vitest'

const calls = vi.hoisted(() => ({ order: [] as string[] }))

// plugins.ts accepts hot updates through webpack's module object, which the test runtime lacks
vi.hoisted(() => { (globalThis as Record<string, unknown>).module = {} })

vi.mock('../src/modules/config/index', () => ({ DataHubModule: { name: 'data-hub' } }))
vi.mock('../src/modules/graphql/index', () => ({ GraphQLModule: { name: 'graphql' } }))
vi.mock('../src/modules/config/dynamic-types/dynamic-type-data-hub-adapter-registry', () => ({ DynamicTypeDataHubAdapterRegistry: {} }))
vi.mock('../src/modules/config/dynamic-types/adapters/dynamic-type-data-hub-adapter-graphql', () => ({ DynamicTypeDataHubAdapterGraphQL: {} }))
vi.mock('../src/modules/operators/dynamic-type-operator-registry', () => ({ DynamicTypeOperatorRegistry: {} }))
vi.mock('../src/modules/operators/operators', () => ({}))
vi.mock('../src/modules/graphql/change-control/register-review-surface', () => ({
  registerChangeControlReviewSurface: vi.fn(() => { calls.order.push('review-surface') })
}))

describe('DataHubPlugin.onStartup', () => {
  it('registers the review surface once, after both modules', async () => {
    const { DataHubPlugin } = await import('../src/plugins')
    const { registerChangeControlReviewSurface } = await import('../src/modules/graphql/change-control/register-review-surface')
    const moduleSystem = {
      registerModule: vi.fn((module: { name: string }) => { calls.order.push(module.name) })
    }

    DataHubPlugin.onStartup?.({ moduleSystem } as unknown as Parameters<NonNullable<typeof DataHubPlugin.onStartup>>[0])

    expect(registerChangeControlReviewSurface).toHaveBeenCalledTimes(1)
    expect(calls.order).toEqual(['data-hub', 'graphql', 'review-surface'])
  })
})
