/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

// Run with: node --test "tests/*.test.mjs"
import assert from 'node:assert/strict'
import { readFileSync } from 'node:fs'
import { describe, it } from 'node:test'

const pkg = JSON.parse(readFileSync(new URL('../package.json', import.meta.url), 'utf8'))
const declared = { ...pkg.devDependencies, ...pkg.dependencies }

const minOf = (range) => range.match(/(\d+)(?:\.(\d+))?(?:\.(\d+))?/).slice(1).map((n) => Number(n ?? 0))
const atLeast = (range, min) => {
  const a = minOf(range)
  const b = min.split('.').map(Number)
  for (let i = 0; i < 3; i++) if (a[i] !== b[i]) return a[i] > b[i]
  return true
}

describe('package.json declares the 2026.4 minimums', () => {
  it('AC1: antd, @rsbuild/core, @types/jest and the MF plugin declare the LTS minimums', () => {
    const minimums = { antd: '5.29.3', '@rsbuild/core': '1.7.6', '@types/jest': '30.0.0', '@module-federation/rsbuild-plugin': '2.9.0', jest: '30.0.0' }
    for (const [name, min] of Object.entries(minimums)) {
      if (declared[name] === undefined) continue
      assert.ok(atLeast(declared[name], min), `${name} ${declared[name]} < ${min}`)
    }
  })

  it('AC3: the lockfile keeps the LTS versions of antd, @rsbuild/core, jest and react', () => {
    const lock = JSON.parse(readFileSync(new URL('../package-lock.json', import.meta.url), 'utf8'))
    const resolved = (name) => lock.packages[`node_modules/${name}`].version
    assert.equal(resolved('antd'), '5.29.3')
    assert.equal(resolved('@rsbuild/core'), '1.7.6')
    assert.equal(resolved('jest'), '30.5.2')
    assert.equal(resolved('react'), '18.3.1')
    // the root entry of the lock mirrors the manifest
    assert.equal(lock.packages[''].dependencies.antd, pkg.dependencies.antd)
  })

  it('AC2: directly imported packages are declared', () => {
    for (const name of ['antd', 'antd-style', 'lodash', '@tanstack/react-table', 'classnames']) {
      assert.ok(declared[name] !== undefined, `${name} is imported but not declared`)
    }
  })
})
