/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

// @vitest-environment jsdom

import React from 'react'
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest'
import { cleanup, render, screen } from '@testing-library/react'
import { registerChangeControlReviewSurface } from '../../src/modules/graphql/change-control/register-review-surface'
import { GraphQLConfigReviewSurface } from '../../src/modules/graphql/change-control/graphql-config-review-surface'
import { useStyles } from '../../src/modules/graphql/change-control/graphql-config-review-surface.styles'

const host = vi.hoisted(() => ({ get: vi.fn() }))

vi.mock('@pimcore/studio-ui-bundle', () => ({ container: { get: host.get } }))

// the surface itself has its own test; here only its identity matters
vi.mock('../../src/modules/graphql/change-control/graphql-config-review-surface', () => ({
  GraphQLConfigReviewSurface: () => null
}))

describe('registerChangeControlReviewSurface', () => {
  beforeEach(() => {
    vi.useFakeTimers()
    host.get.mockReset()
  })

  afterEach(() => {
    vi.useRealTimers()
  })

  it('registers the surface once every module has initialised', () => {
    const registry = { registerDynamicType: vi.fn() }
    host.get.mockReturnValue(registry)

    registerChangeControlReviewSurface()
    expect(host.get).not.toHaveBeenCalled()

    vi.runAllTimers()

    expect(host.get).toHaveBeenCalledWith('ChangeControl/Review/SurfaceRegistry')
    expect(registry.registerDynamicType).toHaveBeenCalledTimes(1)
    expect(registry.registerDynamicType).toHaveBeenCalledWith({
      id: 'data-hub-graphql-config',
      component: GraphQLConfigReviewSurface,
      layout: { width: 1440 }
    })
  })

  it('registers nothing and stays quiet without Change Control', () => {
    host.get.mockImplementation(() => { throw new Error('No binding found') })

    registerChangeControlReviewSurface()

    expect(() => vi.runAllTimers()).not.toThrow()
    expect(host.get).toHaveBeenCalledTimes(1)
  })
})

describe('review surface styles', () => {
  afterEach(cleanup)

  it('gives every region of the split a class', () => {
    const Probe = (): React.JSX.Element => {
      const { styles } = useStyles()
      return <p>{ [styles.split, styles.rail, styles.editor, styles.empty].every((name) => typeof name === 'string' && name !== '') ? 'styled' : 'unstyled' }</p>
    }

    render(<Probe />)

    expect(screen.getByText('styled')).toBeTruthy()
  })
})
