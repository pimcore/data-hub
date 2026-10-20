/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { type Config } from 'jest'

// The studio-ui-bundle SDK is types-only here (its runtime is provided by the host app through module federation),
// so tests map its entry points to the light-weight stand-ins in js/test/mocks.
const mock = (name: string): string => `<rootDir>/js/test/mocks/studio-ui-bundle/${name}.tsx`

const config: Config = {
  testEnvironment: 'jsdom',
  // jsdom defaults to the browser build of dependencies like yaml, which is ESM-only
  testEnvironmentOptions: { customExportConditions: ['node', 'node-addons'] },
  testMatch: ['<rootDir>/js/test/**/*.test.{ts,tsx}'],
  setupFilesAfterEnv: ['<rootDir>/js/test/setup.ts'],
  moduleNameMapper: {
    '^@pimcore/studio-ui-bundle/components$': mock('components'),
    '^@pimcore/studio-ui-bundle/app$': mock('app'),
    '^@pimcore/studio-ui-bundle/api/data-object$': mock('api-data-object'),
    '^@pimcore/studio-ui-bundle/modules/data-object$': mock('modules-data-object'),
    '^@pimcore/studio-ui-bundle/modules/auth$': mock('modules-auth'),
    '^@pimcore/studio-ui-bundle/modules/element$': mock('modules-element')
  },
  transform: {
    '^.+\\.tsx?$': ['@swc/jest', {
      jsc: {
        parser: { syntax: 'typescript', tsx: true },
        transform: { react: { runtime: 'classic' } }
      }
    }]
  }
}

export default config
