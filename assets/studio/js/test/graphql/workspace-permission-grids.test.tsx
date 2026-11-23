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
import { cleanup, fireEvent, render, screen, within } from '@testing-library/react'

// the review's marks on the form, by name path
const annotations: Record<string, { status: string } | undefined> = {}
// the order a reader sorted the grid into; TanStack's row index follows it, the row's original does not
const view = { reversed: false }

vi.mock('@pimcore/studio-ui-bundle/app', () => ({
  useTranslation: () => ({ t: (key: string) => key })
}))

// a grid that renders each row's cells, and the operations slot as the accordion's header button
vi.mock('@pimcore/studio-ui-bundle/components', () => {
  const OperationalGrid = ({ columns, value, children }: { columns: any[], value: any[], children?: React.ReactNode }): React.JSX.Element => (
    <div>
      <table>
        <tbody>
          { (view.reversed ? [...value].reverse() : value).map((row, index) => (
            <tr
              data-testid={ `row-${(row.entity ?? row.name ?? row.path) as string}` }
              key={ row.id ?? row.name }
            >
              { columns.map((column) => (
                <td key={ column.id ?? column.accessorKey }>
                  { column.cell !== undefined ? column.cell({ row: { index, original: row }, getValue: () => row[column.accessorKey] }) : String(row[column.accessorKey]) }
                </td>
              )) }
            </tr>
          )) }
        </tbody>
      </table>
      { children }
    </div>
  )
  OperationalGrid.Operations = function Operations ({ children }: { children: () => React.ReactNode }): React.JSX.Element {
    return <>{ children() }</>
  }
  OperationalGrid.Grid = function Grid (): null {
    return null
  }
  return {
    Flex: ({ children, align, justify, 'data-testid': testId }: { children?: React.ReactNode, align?: string, justify?: string, 'data-testid'?: string }) => (
      <div
        data-align={ align }
        data-justify={ justify }
        data-testid={ testId }
      >{ children }</div>
    ),
    IconButton: ({ icon, onClick }: { icon: { value: string }, onClick: () => void }) => <button onClick={ onClick }>{ icon.value }</button>,
    IconTextButton: ({ children, onClick }: { children?: React.ReactNode, onClick: (event: { stopPropagation: () => void }) => void }) => (
      <button onClick={ () => { onClick({ stopPropagation: () => undefined }) } }>{ children }</button>
    ),
    Accordion: ({ items }: { items: Array<{ key: string, title?: React.ReactNode, info?: React.ReactNode, children?: React.ReactNode }> }) => (
      <div>{ items.map((item) => <div key={ item.key }><div data-testid={ `header-${item.key}` }>{ item.title }</div>{ item.info }{ item.children }</div>) }</div>
    ),
    Modal: ({ open }: { open: boolean }) => open ? <div data-testid="add-modal" /> : null,
    Select: () => null,
    FormAnnotationsProvider: undefined,
    useFormItemAnnotation: (name: string[]) => annotations[name.join('.')],
    OperationalGrid
  }
})

vi.mock('../../src/modules/config/users-api-slice.gen', () => ({
  useBundleDataHubUsersCollectionQuery: () => ({ data: { items: [] } })
}))

const { WorkspaceGrid } = await import('../../src/modules/graphql/components/tabs/security-definition-tab/workspace-grid')
const { PermissionGrid } = await import('../../src/modules/graphql/components/tabs/permissions-tab/permission-grid')
const { WorkspaceRowChangesContext } = await import('../../src/modules/graphql/change-control/workspace-row-changes')

afterEach(() => {
  cleanup()
  for (const key of Object.keys(annotations)) annotations[key] = undefined
  view.reversed = false
})

const workspace = (path: string, flags: Partial<Record<'create' | 'read' | 'update' | 'delete', boolean>> = {}): { path: string, create: boolean, read: boolean, update: boolean, delete: boolean } =>
  ({ path, create: false, read: true, update: false, delete: false, ...flags })

describe('workspace and permission grids', () => {
  it('drops trash and add from the workspace and permission grids in a review', () => {
    render(
      <WorkspaceGrid
        isWriteable={ false }
        type="objects"
        value={ [{ path: '/shop', create: false, read: true, update: false, delete: false }] }
      />
    )
    expect(screen.queryByText('trash')).toBeNull()
    expect(screen.queryByText('add')).toBeNull()
    cleanup()

    render(
      <PermissionGrid
        isWriteable={ false }
        type="users"
        value={ [{ id: 1, name: 'editor', read: true, update: false, delete: false }] }
      />
    )
    expect(screen.queryByText('trash')).toBeNull()
    expect(screen.queryByText('add')).toBeNull()
  })

  it('keeps trash and add in the workspace and permission grids outside a review', () => {
    const onWorkspaces = vi.fn()
    render(
      <WorkspaceGrid
        onChange={ onWorkspaces }
        type="objects"
        value={ [{ path: '/shop', create: false, read: true, update: false, delete: false }] }
      />
    )
    fireEvent.click(screen.getByText('trash'))
    expect(onWorkspaces).toHaveBeenCalledWith([])
    fireEvent.click(screen.getByText('add'))
    expect(onWorkspaces).toHaveBeenLastCalledWith([
      { path: '/shop', create: false, read: true, update: false, delete: false },
      { path: '', create: false, read: true, update: false, delete: false }
    ])
    cleanup()

    const onPermissions = vi.fn()
    render(
      <PermissionGrid
        onChange={ onPermissions }
        type="users"
        value={ [{ id: 1, name: 'editor', read: true, update: false, delete: false }] }
      />
    )
    fireEvent.click(screen.getByText('trash'))
    expect(onPermissions).toHaveBeenCalledWith([])
    expect(screen.queryByTestId('add-modal')).toBeNull()
    fireEvent.click(screen.getByText('add'))
    expect(screen.getByTestId('add-modal')).toBeTruthy()
  })

  it('marks the rows a review adds, changes and drops, centred, and the list in its header', () => {
    annotations['workspaces.objects'] = { status: 'changed' }
    render(
      <WorkspaceRowChangesContext.Provider
        value={ {
          objects: {
            status: 'changed',
            byPath: { '/new': 'added', '/flag': 'changed' },
            removed: [workspace('/dropped')]
          }
        } }
      >
        <WorkspaceGrid
          isWriteable={ false }
          type="objects"
          value={ [workspace('/kept'), workspace('/flag', { update: true }), { ...workspace(''), path: { fullPath: '/new' } as unknown as string }] }
        />
      </WorkspaceRowChangesContext.Provider>
    )

    expect(within(screen.getByTestId('row-/kept')).queryByTestId(/^row-mark-/)).toBeNull()
    expect(within(screen.getByTestId('row-/flag')).getByTestId('row-mark-changed')).toBeTruthy()
    expect(screen.getByTestId('row-mark-added')).toBeTruthy()
    expect(within(screen.getByTestId('row-/dropped')).getByTestId('row-mark-removed')).toBeTruthy()

    const cell = within(screen.getByTestId('row-/flag')).getByTestId('mark-cell')
    expect(cell.getAttribute('data-align')).toBe('center')
    expect(cell.getAttribute('data-justify')).toBe('center')
    expect(within(cell).getByTestId('row-mark-changed').style.marginInlineEnd).toBe('0')

    expect(within(screen.getByTestId('header-objects')).getByTestId('row-mark-changed')).toBeTruthy()
  })

  it('keeps each mark on its own row when the reader sorts the grid', () => {
    view.reversed = true
    render(
      <WorkspaceRowChangesContext.Provider
        value={ { objects: { status: 'changed', byPath: { '/new': 'added', '/flag': 'changed' }, removed: [workspace('/dropped')] } } }
      >
        <WorkspaceGrid
          isWriteable={ false }
          type="objects"
          value={ [workspace('/kept'), workspace('/flag', { update: true }), workspace('/new')] }
        />
      </WorkspaceRowChangesContext.Provider>
    )

    expect(screen.getAllByRole('row').map((row) => row.dataset.testid)).toEqual(['row-/dropped', 'row-/new', 'row-/flag', 'row-/kept'])
    expect(within(screen.getByTestId('row-/dropped')).getByTestId('row-mark-removed')).toBeTruthy()
    expect(within(screen.getByTestId('row-/new')).getByTestId('row-mark-added')).toBeTruthy()
    expect(within(screen.getByTestId('row-/flag')).getByTestId('row-mark-changed')).toBeTruthy()
    expect(within(screen.getByTestId('row-/kept')).queryByTestId(/^row-mark-/)).toBeNull()
  })

  it('falls back to the derived list status for the header without a form annotation', () => {
    render(
      <WorkspaceRowChangesContext.Provider value={ { assets: { status: 'added', byPath: { '/a': 'added' }, removed: [] } } }>
        <WorkspaceGrid
          isWriteable={ false }
          type="assets"
          value={ [workspace('/a')] }
        />
      </WorkspaceRowChangesContext.Provider>
    )

    expect(within(screen.getByTestId('header-assets')).getByTestId('row-mark-added')).toBeTruthy()
    expect(within(screen.getByTestId('row-/a')).getByTestId('row-mark-added')).toBeTruthy()
  })

  it('adds no mark column, row or header mark outside a review or for an unchanged list', () => {
    render(
      <WorkspaceRowChangesContext.Provider value={ { objects: { status: 'changed', byPath: {}, removed: [workspace('/gone')] } } }>
        <WorkspaceGrid
          isWriteable={ false }
          type="documents"
          value={ [workspace('/docs')] }
        />
      </WorkspaceRowChangesContext.Provider>
    )
    expect(screen.queryByTestId('mark-cell')).toBeNull()
    expect(screen.queryByTestId('row-/gone')).toBeNull()
    expect(within(screen.getByTestId('header-documents')).queryByTestId(/^row-mark-/)).toBeNull()
    cleanup()

    render(
      <WorkspaceGrid
        type="objects"
        value={ [workspace('/shop')] }
      />
    )
    expect(screen.queryByTestId('mark-cell')).toBeNull()
    expect(screen.queryByTestId(/^row-mark-/)).toBeNull()
  })
})
