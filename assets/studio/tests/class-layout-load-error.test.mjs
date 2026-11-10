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
  toClassLayoutLoadError
} from '../js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/hooks/class-layout-load-error.ts'

describe('class layout load error', () => {
  it('reports no error when the layout loaded', () => {
    assert.equal(toClassLayoutLoadError(undefined), undefined)
  })

  it('reports a missing permission for a 403 response', () => {
    assert.equal(toClassLayoutLoadError({ status: 403, data: {} }), 'forbidden')
  })

  it('reports any other failure as failed', () => {
    assert.equal(toClassLayoutLoadError({ status: 404, data: {} }), 'failed')
    assert.equal(toClassLayoutLoadError({ status: 'FETCH_ERROR', error: 'TypeError' }), 'failed')
    assert.equal(toClassLayoutLoadError({ name: 'SerializedError', message: 'boom' }), 'failed')
  })
})
