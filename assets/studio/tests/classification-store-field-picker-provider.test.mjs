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
import { resolveClassificationStoreFieldPickerProvider } from '../js/src/modules/config/components/column-config-modal/resolve-classification-store-field-picker-provider.ts'

// pimcore/platform-version#830: the column editor must mount Studio's picker provider when the installed Studio
// exports it, and must not break on Studio versions that do not.
describe('resolveClassificationStoreFieldPickerProvider', () => {
  it('returns the provider when the Studio SDK exports it', () => {
    const Provider = () => null
    assert.equal(resolveClassificationStoreFieldPickerProvider({ ClassificationStoreFieldPickerProvider: Provider }), Provider)
  })

  it('returns undefined on Studio versions without the export', () => {
    assert.equal(resolveClassificationStoreFieldPickerProvider({}), undefined)
  })

  it('ignores a non-component value', () => {
    assert.equal(resolveClassificationStoreFieldPickerProvider({ ClassificationStoreFieldPickerProvider: 'nope' }), undefined)
  })
})
