/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

// Run with: node --test "tests/*.test.mjs" (Node >= 22.18 or 24, which strips TypeScript types natively)
import assert from 'node:assert/strict'
import { describe, it } from 'node:test'
import {
  collectBrickLayoutResults,
  getLayoutLoadErrorMessages,
  isBrickFetchPending,
  toClassLayoutLoadError
} from '../js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/hooks/class-layout-load-error.ts'

const recordingT = () => {
  const keys = []
  const t = (key, options) => {
    keys.push(key)
    return options === undefined ? key : `${key} ${JSON.stringify(options)}`
  }
  return { t, keys }
}

describe('class layout load errors', () => {
  it('classifies the request error', () => {
    assert.equal(toClassLayoutLoadError(undefined), undefined)
    assert.equal(toClassLayoutLoadError(null), undefined)
    assert.equal(toClassLayoutLoadError({ status: 403 }), 'forbidden')
    assert.equal(toClassLayoutLoadError({ status: 500 }), 'failed')
    assert.equal(toClassLayoutLoadError({ status: 'FETCH_ERROR' }), 'failed')
  })

  it('does not look up a load-error translation when nothing failed', () => {
    const { t, keys } = recordingT()
    assert.deepEqual(getLayoutLoadErrorMessages(undefined, [], t), [])
    assert.deepEqual(keys, [])
  })

  it('reports a failed class layout', () => {
    const { t, keys } = recordingT()
    const messages = getLayoutLoadErrorMessages('forbidden', [], t)
    assert.deepEqual(keys, ['data-hub.schema.class-attributes-load-error.forbidden'])
    assert.equal(messages.length, 1)
  })

  it('reports failed brick layouts once per reason, naming the bricks', () => {
    const { t } = recordingT()
    const messages = getLayoutLoadErrorMessages(undefined, [
      { key: 'Engine', error: 'failed' },
      { key: 'Wheels', error: 'forbidden' },
      { key: 'Seats', error: 'failed' }
    ], t)
    assert.deepEqual(messages.map(({ key }) => key), ['bricks-failed', 'bricks-forbidden'])
    assert.equal(messages[0].message, 'data-hub.schema.brick-layouts-load-error.failed {"bricks":"Engine, Seats"}')
    assert.equal(messages[1].message, 'data-hub.schema.brick-layouts-load-error.forbidden {"bricks":"Wheels"}')
  })
})

describe('brick layout results', () => {
  it('keeps the loaded layouts and collects the failed bricks', () => {
    const { layouts, loadErrors } = collectBrickLayoutResults(['Engine', 'Wheels', 'Seats'], [
      { data: { name: 'Engine' } },
      { error: { status: 403 } },
      { error: { status: 500 } }
    ])
    assert.deepEqual([...layouts.keys()], ['Engine'])
    assert.deepEqual(loadErrors, [{ key: 'Wheels', error: 'forbidden' }, { key: 'Seats', error: 'failed' }])
  })

  it('stops waiting for brick layouts when every brick failed', () => {
    const state = { classLayoutLoading: false, brickKeyCount: 2, loadedBrickCount: 0 }
    assert.equal(isBrickFetchPending({ ...state, brickErrorCount: 0 }), true)
    assert.equal(isBrickFetchPending({ ...state, brickErrorCount: 2 }), false)
    assert.equal(isBrickFetchPending({ ...state, loadedBrickCount: 1, brickErrorCount: 1 }), false)
    assert.equal(isBrickFetchPending({ ...state, classLayoutLoading: true, brickErrorCount: 0 }), false)
  })
})
