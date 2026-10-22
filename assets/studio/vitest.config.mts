/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { fileURLToPath } from 'node:url'
import { defineConfig } from 'vitest/config'

// the host bundles are federated at runtime and ship only types; a test mocks what it renders
const federated = (name: string): string => fileURLToPath(new URL(`./js/test/federated/${name}.ts`, import.meta.url))

export default defineConfig({
  resolve: {
    alias: [
      { find: /^@pimcore\/studio-ui-bundle$/, replacement: federated('studio-ui') },
      { find: /^@pimcore\/studio-ui-bundle\/app$/, replacement: federated('studio-ui-app') },
      { find: /^@pimcore\/studio-ui-bundle\/components$/, replacement: federated('studio-ui-components') },
      { find: /^@pimcore\/change-control-bundle\/sdk$/, replacement: federated('change-control-sdk') }
    ]
  },
  test: {
    environment: 'node',
    include: ['js/test/**/*.test.{ts,tsx}']
  }
})
