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
import { cleanup, fireEvent, render, screen, waitFor, within } from '@testing-library/react'

// the host's modules are federated at runtime; these stand in with just enough to observe the surface
const seen = vi.hoisted(() => ({
  annotations: [] as unknown[],
  editor: [] as Array<{ activeTab: string, configuration: Record<string, any> }>,
  liveQuery: [] as Array<{ skip?: boolean }>
}))

vi.mock('@pimcore/studio-ui-bundle/app', () => ({
  useTranslation: () => ({
    t: (key: string, options?: { name?: string, count?: number }) =>
      options?.name !== undefined ? `${key}:${options.name}` : options?.count !== undefined ? `${key}:${options.count}` : key
  })
}))

vi.mock('@pimcore/studio-ui-bundle/components', () => ({
  Content: ({ children }: { children?: React.ReactNode }) => <div>{ children }</div>,
  FormAnnotationsProvider: ({ annotations, children }: { annotations: unknown, children?: React.ReactNode }) => {
    seen.annotations.push(annotations)
    return <>{ children }</>
  },
  useFormItemAnnotation: () => undefined
}))

vi.mock('@pimcore/change-control-bundle/sdk', () => ({
  ReviewOutcomeHeader: () => null,
  ConfigSummary: ({ name, variant, sections, onOpenSection }: {
    name: string
    variant?: string
    sections: Array<{ key: string, label: string, rows: Array<{ key: string, label: string, note?: string }> }>
    onOpenSection: (key: string) => void
  }) => (
    <div
      data-testid="rail"
      data-variant={ variant ?? 'changes' }
    >
      <h2>{ name }</h2>
      { sections.map((section) => (
        <section
          data-testid={ `rail-${section.key}` }
          key={ section.key }
        >
          <button onClick={ () => { onOpenSection(section.key) } }>{ section.label }</button>
          <ul>
            { section.rows.map((row) => <li key={ row.key }>{ row.label }{ row.note !== undefined ? ` (${row.note})` : '' }</li>) }
          </ul>
        </section>
      )) }
    </div>
  )
}))

vi.mock('../../src/modules/graphql/components/graphql-config-editor', () => ({
  GRAPHQL_TABS: { general: 'general', schema: 'schema', security: 'security', permissions: 'permissions' },
  GraphQLConfigEditor: (props: { activeTab: string, configuration: Record<string, any> }) => {
    seen.editor.push(props)
    return <div data-testid="editor">{ props.activeTab }</div>
  }
}))

vi.mock('../../src/modules/config/config-api-slice-enhanced', () => ({
  useBundleDataHubConfigGetQuery: (_args: unknown, options?: { skip?: boolean }) => {
    seen.liveQuery.push(options ?? {})
    return options?.skip === true ? { isLoading: false } : { isLoading: false, data: { configuration: LIVE } }
  }
}))

vi.mock('../../src/modules/graphql/change-control/graphql-config-review-surface.styles', () => ({
  useStyles: () => ({ styles: { split: 'split', rail: 'rail', editor: 'editor', empty: 'empty' } })
}))

const { GraphQLConfigReviewSurface } = await import('../../src/modules/graphql/change-control/graphql-config-review-surface')

const LIVE = {
  general: { name: 'shop', type: 'graphql', active: true, description: 'old' },
  schema: { queryEntities: {} },
  workspaces: { object: [] }
}

const CAR = { 'schema.queryEntities.Car.id': 'Car', 'schema.queryEntities.Car.name': 'Car' }
const WORKSPACE = [{ cpath: '/cars', read: true, create: false, update: false, delete: false }]

const payload = (isNew: boolean, slots: Record<string, unknown>): Record<string, unknown> => ({
  subjectType: 'data-hub-graphql-config',
  subjectRef: 'shop',
  subjects: [{ subjectType: 'data-hub-graphql-config', subjectRef: 'shop', isNew }],
  slots
})

const respond = (body: unknown): void => {
  vi.stubGlobal('fetch', vi.fn(async () => await Promise.resolve({ ok: true, json: async () => await Promise.resolve(body) })))
}

const mount = (): void => {
  render(
    <GraphQLConfigReviewSurface
      {
        ...({ subjectRef: 'shop', changeSetId: 'change-set-1' } as unknown as React.ComponentProps<typeof GraphQLConfigReviewSurface>)
      }
    />
  )
}

const last = <T, >(list: T[]): T | undefined => list[list.length - 1]

const rowsOf = (section: string): string[] =>
  within(screen.getByTestId(`rail-${section}`)).getAllByRole('listitem').map((item) => item.textContent ?? '')

beforeEach(() => {
  seen.annotations.length = 0
  seen.editor.length = 0
  seen.liveQuery.length = 0
})

afterEach(() => {
  cleanup()
  vi.unstubAllGlobals()
})

describe('GraphQLConfigReviewSurface', () => {
  it('marks every changed field and lists it in the rail under its tab', async () => {
    respond(payload(false, {
      general: { shape: 'record', current: { 'general.description': 'old' }, proposed: { 'general.description': 'new' } },
      schema: { shape: 'record', current: {}, proposed: CAR },
      security: { shape: 'record', current: { 'workspaces.object': [] }, proposed: { 'workspaces.object': WORKSPACE } }
    }))

    mount()

    await waitFor(() => { expect(screen.getByTestId('rail').dataset.variant).toBe('changes') })

    expect(rowsOf('general')).toEqual(['Description'])
    expect(rowsOf('schema')).toEqual(['data-hub.review.field.query-entity:Car'])
    expect(rowsOf('security')).toEqual(['Object (0 → 1)'])

    expect(last(seen.annotations)).toEqual({
      description: { status: 'changed' },
      'schema.query': { status: 'added' },
      'workspaces.objects': { status: 'changed' }
    })

    // the editor shows the live document with the proposal laid over it, on the first change's tab
    const editor = last(seen.editor)
    expect(editor?.activeTab).toBe('general')
    expect(editor?.configuration.general.description).toBe('new')
    expect(editor?.configuration.general.active).toBe(true)
    expect(last(seen.liveQuery)?.skip).toBe(false)

    fireEvent.click(within(screen.getByTestId('rail-security')).getByRole('button'))
    expect(screen.getByTestId('editor').textContent).toBe('security')
  })

  it('tells a new configuration per tab instead of marking every field', async () => {
    respond(payload(true, {
      general: { shape: 'record', current: null, proposed: { 'general.name': 'shop', 'general.active': false, 'general.group': 'cars' } },
      schema: { shape: 'record', current: null, proposed: CAR },
      security: { shape: 'record', current: null, proposed: { 'workspaces.object': WORKSPACE } }
    }))

    mount()

    await waitFor(() => { expect(screen.getByTestId('rail').dataset.variant).toBe('description') })

    expect(rowsOf('general')).toEqual(['Group cars'])
    expect(rowsOf('schema')).toEqual(['Car'])
    expect(rowsOf('security')).toEqual(['data-hub.review.outline.rules-one:1'])

    // a configuration that does not exist yet has no live side to load, and nothing is marked
    expect(seen.liveQuery.every((options) => options.skip === true)).toBe(true)
    expect(last(seen.annotations)).toEqual({})
  })

  it('says the review is unavailable when the change set cannot be read', async () => {
    vi.stubGlobal('fetch', vi.fn(async () => await Promise.resolve({ ok: false, status: 403 })))

    mount()

    expect(await screen.findByText('data-hub.review.unavailable')).toBeTruthy()
    expect(screen.queryByTestId('editor')).toBeNull()
  })
})
