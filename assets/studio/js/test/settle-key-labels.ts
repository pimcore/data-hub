/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { act } from '@testing-library/react'

/**
 * Settles the Classification Store key-label lookup the column editor starts for a configured key.
 *
 * It answers over a chain of microtasks, which drain after the test body returns and before the
 * afterEach hooks run - outside act either way - so a test that mounts the editor with a per-key
 * store column has to await it here rather than in a hook.
 */
export const settleKeyLabels = async (): Promise<void> => {
  await act(async () => { await new Promise(resolve => setTimeout(resolve, 0)) })
}
