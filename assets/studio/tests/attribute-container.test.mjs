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
  isAttributeContainer
} from '../js/src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/hooks/attribute-container.ts'

describe('attribute container', () => {
  it('treats localized fields as a container of columns', () => {
    assert.equal(isAttributeContainer('localizedfields'), true)
  })

  it('treats field types with a column of their own as columns', () => {
    for (const fieldType of ['input', 'block', 'fieldcollections', 'manyToManyObjectRelation', 'objectbricks']) {
      assert.equal(isAttributeContainer(fieldType), false, fieldType)
    }
  })

  it('treats a missing field type as a column', () => {
    assert.equal(isAttributeContainer(undefined), false)
  })
})
