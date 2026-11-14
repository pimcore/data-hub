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
import { cleanup, fireEvent, render, screen } from '@testing-library/react'

// the host's modules are federated at runtime; these stand in with just enough to observe the editor
const seen = vi.hoisted(() => {
  const fields: Record<string, unknown> = {}
  return {
    detailView: [] as Array<Record<string, any>>,
    formKit: [] as Array<Record<string, any>>,
    fields,
    handleSave: (() => undefined) as () => void,
    updateConfig: (() => undefined) as (args: unknown) => unknown
  }
})

vi.mock('@pimcore/studio-ui-bundle/app', () => ({
  useTranslation: () => ({ t: (key: string) => key })
}))

vi.mock('@pimcore/studio-ui-bundle/components', () => ({
  Content: ({ children }: { children?: React.ReactNode }) => <div>{ children }</div>,
  ContentLayout: ({ renderToolbar, children }: { renderToolbar?: React.ReactNode, children?: React.ReactNode }) => (
    <div>
      <div data-testid="toolbar">{ renderToolbar }</div>
      { children }
    </div>
  ),
  FormKit: ({ formProps, children }: { formProps: Record<string, any>, children?: React.ReactNode }) => {
    seen.formKit.push(formProps)
    return <div>{ children }</div>
  },
  Tabs: ({ activeKey, defaultActiveKey, items, onChange }: {
    activeKey?: string
    defaultActiveKey: string
    items: Array<{ key: string, label: string, children: React.ReactNode }>
    onChange?: (key: string) => void
  }) => {
    const shown = activeKey ?? defaultActiveKey
    return (
      <div
        data-active={ activeKey ?? '' }
        data-testid="tabs"
      >
        { items.map((item) => (
          <button
            key={ item.key }
            onClick={ () => { onChange?.(item.key) } }
          >{ item.label }</button>
        )) }
        { items.filter((item) => item.key === shown).map((item) => <div key={ item.key }>{ item.children }</div>) }
      </div>
    )
  },
  IconTextButton: ({ onClick, children }: { onClick: () => void, children?: React.ReactNode }) => (
    <button onClick={ onClick }>{ children }</button>
  )
}))

vi.mock('../../src/components/base-detail-view', () => ({
  useDetailView: (options: Record<string, any>) => {
    seen.detailView.push(options)
    return {
      form: { getFieldValue: (name: string[]) => seen.fields[name.join('.')] },
      isDirty: true,
      initialValues: { general: { name: options.configName } },
      handleSave: seen.handleSave,
      handleValuesChange: () => undefined
    }
  },
  ConfigToolbar: ({ additionalButtons, isDirty, onSave }: { additionalButtons: React.ReactNode[], isDirty: boolean, onSave: () => void }) => (
    <div data-dirty={ String(isDirty) }>
      { additionalButtons }
      <button onClick={ onSave }>save</button>
    </div>
  ),
  trackConfigError: () => undefined
}))

vi.mock('../../src/components/base-detail-view/base-detail-view.styles', () => ({
  useStyles: () => ({ styles: { formWrapper: 'form-wrapper' } })
}))

vi.mock('../../src/modules/graphql/components/tabs/general-tab', () => ({ GeneralTab: () => <p>general pane</p> }))
vi.mock('../../src/modules/graphql/components/tabs/schema-definition-tab', () => ({ SchemaDefinitionTab: () => <p>schema pane</p> }))
vi.mock('../../src/modules/graphql/components/tabs/security-definition-tab', () => ({ SecurityDefinitionTab: () => <p>security pane</p> }))
vi.mock('../../src/modules/graphql/components/tabs/permissions-tab', () => ({ PermissionsTab: () => <p>permissions pane</p> }))

vi.mock('../../src/modules/graphql/graphql-api-slice-enhanced', () => ({
  useBundleDataHubGraphqlExplorerUrlQuery: () => ({ data: { explorerUrl: 'https://example.test/explorer' } })
}))

vi.mock('../../src/modules/config/config-api-slice-enhanced', () => ({
  useBundleDataHubConfigGetQuery: () => ({
    data: {
      configuration: { general: { name: 'shop', type: 'graphql' } },
      userPermissions: { update: true, delete: true },
      modificationDate: 5
    },
    isLoading: false,
    isFetching: false,
    refetch: () => undefined,
    requestId: 'request-1'
  }),
  useBundleDataHubConfigUpdateMutation: () => [seen.updateConfig, { isLoading: false }]
}))

const { GraphQLConfigEditor } = await import('../../src/modules/graphql/components/graphql-config-editor')
const { GraphQLDetailView } = await import('../../src/modules/graphql/components/graphql-detail-view')

const CONFIGURATION = { general: { name: 'shop', type: 'graphql' } }

const mountDetailView = (): void => {
  render(
    <GraphQLDetailView
      {
        ...({ configName: 'shop', onChange: vi.fn(), onDelete: vi.fn() } as unknown as React.ComponentProps<typeof GraphQLDetailView>)
      }
    />
  )
}

const last = <T, >(list: T[]): T | undefined => list[list.length - 1]

beforeEach(() => {
  seen.detailView.length = 0
  seen.formKit.length = 0
  seen.fields = {}
  seen.handleSave = vi.fn()
  seen.updateConfig = vi.fn(() => ({ unwrap: async () => await Promise.resolve({ modificationDate: 9 }) }))
})

afterEach(() => {
  cleanup()
  vi.unstubAllGlobals()
})

describe('GraphQLConfigEditor', () => {
  it('renders the handed configuration on the tab it is told to show', () => {
    const onTabChange = vi.fn()
    const onSave = vi.fn()

    render(
      <GraphQLConfigEditor
        activeTab="security"
        configName="shop"
        configuration={ CONFIGURATION as any }
        isWriteable={ false }
        onSave={ onSave }
        onTabChange={ onTabChange }
      />
    )

    expect(screen.getAllByRole('button').map((button) => button.textContent)).toEqual([
      'data-hub.tabs.general',
      'data-hub.tabs.schema-definition',
      'data-hub.tabs.security-definition',
      'data-hub.tabs.permissions'
    ])
    expect(screen.getByTestId('tabs').dataset.active).toBe('security')
    expect(screen.getByText('security pane')).toBeTruthy()

    fireEvent.click(screen.getByText('data-hub.tabs.schema-definition'))
    expect(onTabChange).toHaveBeenCalledWith('schema')

    // a read-only mount: the form is disabled and the shared form state gets the handed document
    expect(last(seen.formKit)?.disabled).toBe(true)
    expect(last(seen.detailView)).toMatchObject({ configName: 'shop', configData: CONFIGURATION, isWriteable: false, onSave })
  })

  it('keeps its own tab state when nobody drives it, and renders no form while loading', () => {
    const { rerender } = render(
      <GraphQLConfigEditor
        configName="shop"
        configuration={ CONFIGURATION as any }
        isWriteable
        onSave={ vi.fn() }
      />
    )

    expect(screen.getByTestId('tabs').dataset.active).toBe('')
    expect(screen.getByText('general pane')).toBeTruthy()
    expect(last(seen.formKit)?.disabled).toBe(false)

    rerender(
      <GraphQLConfigEditor
        configName="shop"
        configuration={ CONFIGURATION as any }
        isLoading
        isWriteable
        onSave={ vi.fn() }
      />
    )
    expect(screen.queryByTestId('tabs')).toBeNull()
  })

  it('hands the toolbar what only the form knows', () => {
    seen.fields = { 'security.method': 'datahub_apikey' }
    const renderToolbar = vi.fn(() => <span>toolbar</span>)

    render(
      <GraphQLConfigEditor
        configName="shop"
        configuration={ CONFIGURATION as any }
        isWriteable
        onSave={ vi.fn() }
        renderToolbar={ renderToolbar }
      />
    )

    const state = (renderToolbar.mock.calls as unknown as Array<[{ isDirty: boolean, onSave: () => void, fieldValue: (name: string[]) => unknown }]>)[0][0]
    expect(state.isDirty).toBe(true)
    expect(state.onSave).toBe(seen.handleSave)
    expect(state.fieldValue(['security', 'method'])).toBe('datahub_apikey')
    expect(screen.getByText('toolbar')).toBeTruthy()
  })
})

describe('GraphQLDetailView', () => {
  it('opens the explorer with the first API key the form holds', () => {
    const open = vi.fn()
    vi.stubGlobal('open', open)
    seen.fields = { 'security.method': 'datahub_apikey', 'security.apikey': 'first-key\nsecond-key' }

    mountDetailView()

    fireEvent.click(screen.getByText('data-hub.open-in-tab'))
    expect(open).toHaveBeenCalledWith('https://example.test/explorer?apikey=first-key', '_blank')
  })

  it('opens the explorer without a key when the endpoint does not use API keys', () => {
    const open = vi.fn()
    vi.stubGlobal('open', open)
    seen.fields = { 'security.method': 'other' }

    mountDetailView()

    fireEvent.click(screen.getByText('data-hub.open-in-tab'))
    expect(open).toHaveBeenCalledWith('https://example.test/explorer', '_blank')
  })

  it('saves through the editor\'s form state to the update API', async () => {
    mountDetailView()

    fireEvent.click(screen.getByText('save'))
    expect(seen.handleSave).toHaveBeenCalledTimes(1)

    const options = last(seen.detailView)
    expect(options).toMatchObject({ configName: 'shop', configData: CONFIGURATION, isWriteable: true, modificationDate: 5 })

    const saved = await options?.onSave({ general: { name: 'shop', description: 'new' } }, 5)
    expect(seen.updateConfig).toHaveBeenCalledWith({
      name: 'shop',
      bundleDataHubUpdateConfiguration: { data: JSON.stringify({ general: { name: 'shop', description: 'new' } }), modificationDate: 5 }
    })
    expect(saved).toEqual({ modificationDate: 9 })
  })
})
