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
  buildBrickAttribute,
  buildBrickColumnTitle,
  getAmbiguousBrickKeys
} from '../js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/hooks/brick-container-helpers.ts'

const fields = [
  { name: 'primary', title: 'Primary', allowedTypes: ['Engine', 'Wheels'] },
  { name: 'secondary', title: null, allowedTypes: ['Engine'] }
]

describe('brick container helpers', () => {
  it('finds bricks which are allowed in more than one objectbricks field', () => {
    assert.deepEqual([...getAmbiguousBrickKeys(fields)], ['Engine'])
  })

  it('finds no ambiguous brick when every brick has a single field', () => {
    assert.equal(getAmbiguousBrickKeys([fields[0]]).size, 0)
    assert.equal(getAmbiguousBrickKeys([]).size, 0)
  })

  it('does not count a brick twice for the same field', () => {
    const duplicated = [{ name: 'primary', title: null, allowedTypes: ['Engine', 'Engine'] }]

    assert.equal(getAmbiguousBrickKeys(duplicated).size, 0)
  })

  it('keeps the existing attribute format for unambiguous bricks', () => {
    const ambiguous = getAmbiguousBrickKeys(fields)

    assert.equal(buildBrickAttribute('Wheels', 'size', 'primary', ambiguous), 'Wheels~size')
  })

  it('adds the containing field to the attribute of ambiguous bricks', () => {
    const ambiguous = getAmbiguousBrickKeys(fields)

    assert.equal(buildBrickAttribute('Engine', 'power', 'primary', ambiguous), 'Engine~power~primary')
    assert.equal(buildBrickAttribute('Engine', 'power', 'secondary', ambiguous), 'Engine~power~secondary')
  })

  it('names the containing field in the title of ambiguous brick columns only', () => {
    const ambiguous = getAmbiguousBrickKeys(fields)

    assert.equal(buildBrickColumnTitle('Power', 'Engine', 'power', 'secondary', ambiguous), 'Power (secondary.Engine.power)')
    assert.equal(buildBrickColumnTitle('Size', 'Wheels', 'size', 'primary', ambiguous), 'Size (Wheels.size)')
  })
})
