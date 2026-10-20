/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import '@testing-library/jest-dom'

// jsdom has no crypto.randomUUID
let uuidCounter = 0
Object.defineProperty(globalThis, 'crypto', {
  value: { ...globalThis.crypto, randomUUID: () => `00000000-0000-4000-8000-${String(++uuidCounter).padStart(12, '0')}` }
})
