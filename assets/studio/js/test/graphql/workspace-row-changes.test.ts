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
import { configChanges } from '../../src/modules/graphql/change-control/config-review-model'
import { workspacePathOf, workspaceRowChanges } from '../../src/modules/graphql/change-control/workspace-row-changes'

type Leaves = Record<string, unknown>

const slot = (current: Leaves | null, proposed: Leaves | null): ReviewSlotData =>
  ({ shape: 'record', current, proposed, base: current })

const review = (slots: Record<string, ReviewSlotData>): ChangeSetReviewData =>
  ({ slots }) as unknown as ChangeSetReviewData

const row = (cpath: string, flags: Partial<Record<'create' | 'read' | 'update' | 'delete', boolean>> = {}): Leaves =>
  ({ cpath, create: false, read: true, update: false, delete: false, ...flags })

describe('workspaceRowChanges', () => {
  it('splits a changed list into added, changed and removed rows, matched by path', () => {
    const changes = configChanges(review({
      security: slot(
        { 'workspaces.object': [row('/kept'), row('/flag'), row('/dropped')] },
        { 'workspaces.object': [row('/kept'), row('/flag', { update: true }), row('/new')] }
      )
    }))

    expect(workspaceRowChanges(changes)).toEqual({
      objects: {
        status: 'changed',
        byPath: { '/flag': 'changed', '/new': 'added' },
        removed: [{ path: '/dropped', create: false, read: true, update: false, delete: false }]
      }
    })
  })

  it('keys each list by the grid that shows it and leaves unchanged lists out', () => {
    const changes = configChanges(review({
      security: slot(
        { 'workspaces.document': [row('/a')], 'workspaces.asset': [row('/b')] },
        { 'workspaces.document': [row('/a')], 'workspaces.asset': [row('/b', { delete: true })], 'workspaces.object': [row('/c')] }
      )
    }))

    const result = workspaceRowChanges(changes)
    expect(result.documents).toBeUndefined()
    expect(result.assets?.byPath).toEqual({ '/b': 'changed' })
    expect(result.objects).toEqual({ status: 'added', byPath: { '/c': 'added' }, removed: [] })
  })

  it('marks every row of a list the proposal drops as removed', () => {
    const changes = configChanges(review({
      security: slot({ 'workspaces.asset': [row('/x')] }, {})
    }))

    expect(workspaceRowChanges(changes).assets).toEqual({
      status: 'removed',
      byPath: {},
      removed: [{ path: '/x', create: false, read: true, update: false, delete: false }]
    })
  })

  it('has nothing to mark without a workspace change', () => {
    expect(workspaceRowChanges([])).toEqual({})
  })
})

describe('workspacePathOf', () => {
  it('reads a path or a picked element reference', () => {
    expect(workspacePathOf('/shop')).toBe('/shop')
    expect(workspacePathOf({ fullPath: '/shop/cars' })).toBe('/shop/cars')
    expect(workspacePathOf(null)).toBe('')
  })
})
