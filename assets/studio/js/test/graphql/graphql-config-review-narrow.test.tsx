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
import { afterEach, describe, expect, it, vi } from 'vitest'
import { cleanup, render } from '@testing-library/react'

vi.mock('@pimcore/studio-ui-bundle/app', () => ({ useTranslation: () => ({ t: (key: string) => key }) }))

vi.mock('@pimcore/studio-ui-bundle/components', () => ({
  Content: ({ className, children }: { className?: string, children?: React.ReactNode }) => (
    <div
      className={ className }
      data-testid="surface"
    >{ children }</div>
  ),
  FormAnnotationsProvider: ({ children }: { children?: React.ReactNode }) => <>{ children }</>,
  useFormItemAnnotation: () => undefined
}))

vi.mock('@pimcore/change-control-bundle/sdk', () => ({ ReviewOutcomeHeader: () => null, ConfigSummary: () => null }))

vi.mock('../../src/modules/graphql/components/graphql-config-editor', () => ({
  GRAPHQL_TABS: { general: 'general', schema: 'schema', security: 'security', permissions: 'permissions' },
  GraphQLConfigEditor: () => null
}))

vi.mock('../../src/modules/config/config-api-slice-enhanced', () => ({
  useBundleDataHubConfigGetQuery: () => ({ isLoading: false })
}))

const { GraphQLConfigReviewSurface } = await import('../../src/modules/graphql/change-control/graphql-config-review-surface')
const { EDITOR_BELOW_WIDTH, RAIL_GAP, RAIL_WIDTH, SURFACE_CONTAINER, useStyles } = await import('../../src/modules/graphql/change-control/graphql-config-review-surface.styles')

// jsdom lays nothing out, so a container query is read from the injected rules, not measured
const declaredRules = (element: Element): Array<{ container: string, style: CSSStyleDeclaration }> => {
  const classes = [...element.classList].map((name) => `.${name}`)
  const found: Array<{ container: string, style: CSSStyleDeclaration }> = []
  const visit = (rules: CSSRuleList, container: string): void => {
    for (const rule of [...rules]) {
      if (rule instanceof CSSStyleRule) {
        if (classes.includes(rule.selectorText)) found.push({ container, style: rule.style })
      } else if ('cssRules' in rule && rule.cssText.startsWith('@container')) {
        visit((rule as CSSGroupingRule).cssRules, /^@container\s+([^{]*?)\s*\{/.exec(rule.cssText)?.[1] ?? '')
      }
    }
  }
  for (const sheet of [...document.styleSheets]) visit(sheet.cssRules, '')

  return found
}

const valueOf = (element: Element, container: string, property: string): string => {
  const values = declaredRules(element).filter((rule) => rule.container === container)
    .map((rule) => rule.style.getPropertyValue(property)).filter((value) => value !== '')

  return values[values.length - 1] ?? ''
}

const Probe = (): React.JSX.Element => {
  const { styles } = useStyles()

  return (
    <div
      className={ styles.frame }
      data-testid="frame"
    >
      <div
        className={ styles.split }
        data-testid="split"
      >
        <div
          className={ styles.rail }
          data-testid="rail"
        />
        <div
          className={ styles.editor }
          data-testid="editor"
        />
      </div>
    </div>
  )
}

const byId = (id: string): Element => document.querySelector(`[data-testid="${id}"]`)!

afterEach(() => {
  cleanup()
  vi.unstubAllGlobals()
})

describe('GraphQLConfigReviewSurface in a narrow change request tab', () => {
  // the change request tab with the chat open at 1280px leaves the subject pane about 490px
  const NARROW_PANE = 490
  const narrow = `${SURFACE_CONTAINER} (max-width: ${EDITOR_BELOW_WIDTH - 1}px)`

  it('measures its own width, so the narrow layout follows the pane rather than the window', () => {
    vi.stubGlobal('fetch', vi.fn(async () => await new Promise(() => {})))
    render(
      <GraphQLConfigReviewSurface
        { ...({ subjectRef: 'shop', changeSetId: 'change-set-1' } as unknown as React.ComponentProps<typeof GraphQLConfigReviewSurface>) }
      />
    )
    const surface = byId('surface')

    expect(valueOf(surface, '', 'container-name')).toBe(SURFACE_CONTAINER)
    expect(valueOf(surface, '', 'container-type')).toBe('inline-size')
  })

  it('puts the editor below the list at full width where the pane is too narrow for both', () => {
    render(<Probe />)

    expect(NARROW_PANE).toBeLessThan(EDITOR_BELOW_WIDTH)
    expect(valueOf(byId('split'), narrow, 'flex-direction')).toBe('column')
    expect(valueOf(byId('rail'), narrow, 'width')).toBe('auto')
    expect(valueOf(byId('rail'), narrow, 'flex')).toBe('0 1 auto')
    // the list scrolls in its share; the editor keeps the rest of the height
    expect(valueOf(byId('rail'), narrow, 'max-height')).toBe('40%')
    expect(valueOf(byId('rail'), narrow, 'border-right')).toBe('none')
  })

  it('stacks wherever the editor beside the list would be narrower than its widest table', () => {
    // the security workspace grid's fixed columns: path 300, four permissions of 80, actions 60
    const workspaceGrid = 300 + 4 * 80 + 60
    // a wider window with the chat open: the pane is still too narrow for list and table
    const MEDIUM_PANE = 800

    expect(EDITOR_BELOW_WIDTH - RAIL_WIDTH - RAIL_GAP).toBeGreaterThan(workspaceGrid)
    expect(MEDIUM_PANE).toBeLessThan(EDITOR_BELOW_WIDTH)
  })

  it('keeps the list beside the editor where there is room', () => {
    render(<Probe />)

    expect(valueOf(byId('split'), '', 'flex-direction')).toBe('')
    expect(valueOf(byId('rail'), '', 'flex')).toBe('0 0 320px')
    expect(valueOf(byId('editor'), '', 'min-width')).toBe('0')
  })
})
