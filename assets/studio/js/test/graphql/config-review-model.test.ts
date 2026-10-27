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
import {
  annotationsFor, configChanges, groupChanges, isNewConfiguration, listSizeChange, proposedConfiguration, toFormPath
} from '../../src/modules/graphql/change-control/config-review-model'

type Leaves = Record<string, unknown>

const slot = (current: Leaves | null, proposed: Leaves | null, base: Leaves | null = current): ReviewSlotData =>
  ({ shape: 'record', current, proposed, base })

const review = (slots: Record<string, ReviewSlotData>): ChangeSetReviewData =>
  ({ slots }) as unknown as ChangeSetReviewData

const CAR = { 'schema.queryEntities.Car.id': 'Car', 'schema.queryEntities.Car.name': 'Car' }
const PRODUCT = { 'schema.queryEntities.Product.id': 'Product', 'schema.queryEntities.Product.name': 'Product' }

describe('configChanges', () => {
  it('lists a changed field with its form path and skips identity', () => {
    const changes = configChanges(review({
      general: slot(
        { 'general.name': 'shop', 'general.description': 'old' },
        { 'general.name': 'shop', 'general.description': 'new', 'general.type': 'graphql' }
      )
    }))

    expect(changes).toEqual([{
      address: 'general.description',
      formPath: 'description',
      label: 'description',
      section: 'general',
      status: 'changed',
      current: 'old',
      proposed: 'new'
    }])
  })

  it('lists a field the proposal drops as removed', () => {
    const changes = configChanges(review({
      general: slot({ 'general.group': 'shop', 'general.active': true }, { 'general.active': true })
    }))

    expect(changes).toEqual([expect.objectContaining({
      address: 'general.group', status: 'removed', current: 'shop', proposed: undefined
    })])
  })

  it('names a schema entity the proposal drops once, as removed', () => {
    const changes = configChanges(review({ schema: slot({ ...CAR, ...PRODUCT }, PRODUCT) }))

    expect(changes).toEqual([expect.objectContaining({
      address: 'schema.queryEntities.Car', label: 'Car', status: 'removed', formPath: 'schema.query'
    })])
  })

  it('names a schema entity the proposal adds once, as added', () => {
    const changes = configChanges(review({ schema: slot(PRODUCT, { ...CAR, ...PRODUCT }) }))

    expect(changes).toEqual([expect.objectContaining({
      address: 'schema.queryEntities.Car', label: 'Car', status: 'added', current: undefined, proposed: undefined
    })])
  })

  it('keeps a leaf added to an entity that already exists as that leaf', () => {
    const changes = configChanges(review({
      schema: slot(CAR, { ...CAR, 'schema.queryEntities.Car.columnConfig': [{ attribute: 'sku' }] })
    }))

    expect(changes).toEqual([expect.objectContaining({
      address: 'schema.queryEntities.Car.columnConfig', status: 'added'
    })])
  })

  it('reads a resolved change set against its base', () => {
    const resolved = review({ general: slot({ 'general.group': 'new' }, { 'general.group': 'new' }, { 'general.group': 'old' }) })

    expect(configChanges(resolved, 'current')).toEqual([])
    expect(configChanges(resolved, 'base')).toEqual([expect.objectContaining({ address: 'general.group', status: 'changed' })])
  })

  it('has nothing to list without a payload', () => {
    expect(configChanges(undefined)).toEqual([])
  })
})

describe('proposedConfiguration', () => {
  const live = {
    general: { name: 'shop', active: true, description: 'live', group: 'shop' },
    security: { method: 'datahub_apikey' },
    schema: {
      queryEntities: [{ id: 'Car', name: 'Car' }, { id: 'Product', name: 'Product' }],
      specialEntities: [{ name: 'asset', readAllowed: true, createAllowed: false, updateAllowed: false, deleteAllowed: false }]
    }
  } as unknown as BackendConfiguration

  it('lays the proposed leaves over the live document and keeps what a proposal never carries', () => {
    const configuration = proposedConfiguration(review({
      general: slot({ 'general.description': 'live' }, { 'general.description': 'proposed' }),
      schema: slot(
        { ...CAR, ...PRODUCT, 'schema.specialEntities.asset.read': true },
        { ...CAR, ...PRODUCT, 'schema.specialEntities.asset.read': true, 'schema.specialEntities.document.read': true }
      )
    }), live)

    expect(configuration.general?.description).toBe('proposed')
    expect(configuration.security).toEqual({ method: 'datahub_apikey' })
    expect(configuration.schema?.queryEntities).toEqual({ Car: { id: 'Car', name: 'Car' }, Product: { id: 'Product', name: 'Product' } })
    expect(configuration.schema?.specialEntities).toEqual([
      expect.objectContaining({ name: 'asset', readAllowed: true, createAllowed: false }),
      expect.objectContaining({ name: 'document', readAllowed: true, createAllowed: false, deleteAllowed: false })
    ])
  })

  it('drops what the proposal removes, the entity with its last leaf', () => {
    const configuration = proposedConfiguration(review({
      general: slot({ 'general.group': 'shop' }, {}),
      schema: slot({ ...CAR, ...PRODUCT }, PRODUCT)
    }), live)

    expect(configuration.schema?.queryEntities).toEqual({ Product: { id: 'Product', name: 'Product' } })
    expect(configuration.general).not.toHaveProperty('group')
    expect(configuration.general?.name).toBe('shop')
  })

  it('is the live document without a payload, and the proposal alone without a live one', () => {
    expect(proposedConfiguration(undefined, live).general?.description).toBe('live')
    expect(proposedConfiguration(review({ general: slot(null, { 'general.name': 'new' }) }), undefined))
      .toEqual({ general: { name: 'new' }, schema: {} })
  })
})

describe('isNewConfiguration', () => {
  it('is new only when no slot has anything to compare against', () => {
    expect(isNewConfiguration(review({ general: slot(null, { 'general.name': 'new' }) }))).toBe(true)
    expect(isNewConfiguration(review({ general: slot({ 'general.name': 'shop' }, { 'general.name': 'shop' }) }))).toBe(false)
    expect(isNewConfiguration(review({}))).toBe(false)
    expect(isNewConfiguration(undefined)).toBe(false)
  })

  it('prefers the subject answer over the slots', () => {
    const withSubject = (isNew: boolean): ChangeSetReviewData => ({
      subjectType: 'data-hub-graphql-config',
      subjectRef: 'shop',
      subjects: [
        { subjectType: 'data-hub-graphql-config', subjectRef: 'other', isNew: !isNew },
        { subjectType: 'data-hub-graphql-config', subjectRef: 'shop', isNew }
      ],
      // an edit that adds a section the live document lacks: nothing to compare against
      slots: { schema: slot(null, CAR, null) }
    }) as unknown as ChangeSetReviewData

    expect(isNewConfiguration(withSubject(false))).toBe(false)
    expect(isNewConfiguration(withSubject(true))).toBe(true)
    expect(isNewConfiguration(withSubject(false), 'base')).toBe(false)
  })
})

describe('listSizeChange', () => {
  const change = (current: unknown, proposed: unknown): Parameters<typeof listSizeChange>[0] =>
    ({ address: 'workspaces.object', label: 'object', section: 'security', status: 'changed', current, proposed })

  it('says how many entries a changed list had and has', () => {
    expect(listSizeChange(change([1, 2, 3], [1]))).toBe('3 → 1')
    expect(listSizeChange(change([], [{ cpath: '/' }]))).toBe('0 → 1')
  })

  it('says nothing when the size stays or one side is no list', () => {
    expect(listSizeChange(change([1, 2], [2, 3]))).toBeUndefined()
    expect(listSizeChange(change(undefined, [1]))).toBeUndefined()
    expect(listSizeChange(change('a', 'b'))).toBeUndefined()
  })
})

describe('groupChanges', () => {
  it('orders the groups as the editor shows its tabs, unknown sections last', () => {
    const changes = configChanges(review({
      extra: slot({}, { 'extra.flag': true }),
      security: slot({}, { 'workspaces.object': [{ cpath: '/', read: true }] }),
      general: slot({ 'general.group': 'a' }, { 'general.group': 'b' })
    }))

    expect(groupChanges(changes).map((group) => [group.section, group.label, group.changes.length])).toEqual([
      ['general', 'data-hub.tabs.general', 1],
      ['security', 'data-hub.tabs.security-definition', 1],
      ['extra', 'extra', 1]
    ])
  })
})

describe('form paths and annotations', () => {
  it('maps a document path to the field the form binds', () => {
    expect(toFormPath('general.active')).toBe('active')
    expect(toFormPath('general.name')).toBeUndefined()
    expect(toFormPath('workspaces.asset')).toBe('workspaces.assets')
    expect(toFormPath('schema.mutationEntities.Car.create')).toBe('schema.mutation')
    expect(toFormPath('schema.specialEntities.asset.read')).toBe('schema.genericTypes')
    expect(toFormPath('schema.unknown.x')).toBeUndefined()
    expect(toFormPath('custom.key')).toBe('custom.key')
  })

  it('marks every bound field with its status and leaves unbound ones out', () => {
    const changes = configChanges(review({
      general: slot({ 'general.group': 'a', 'general.foo': 1 }, { 'general.foo': 2 }),
      schema: slot({ ...CAR, ...PRODUCT }, PRODUCT)
    }))

    expect(annotationsFor(changes)).toEqual({
      group: { status: 'removed' },
      'schema.query': { status: 'removed' }
    })
  })
})
