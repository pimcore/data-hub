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
import { cleanup, fireEvent, render, screen } from '@testing-library/react'

vi.mock('@pimcore/studio-ui-bundle/app', () => ({
  useTranslation: () => ({ t: (key: string) => key })
}))

// a grid that renders each row's cells, and the operations slot as the accordion's header button
vi.mock('@pimcore/studio-ui-bundle/components', () => {
  const OperationalGrid = ({ columns, value, children }: { columns: any[], value: any[], children?: React.ReactNode }): React.JSX.Element => (
    <div>
      <table>
        <tbody>
          { value.map((row, index) => (
            <tr
              data-testid={ `row-${(row.entity ?? row.name) as string}` }
              key={ row.id ?? row.name }
            >
              { columns.map((column) => (
                <td key={ column.id ?? column.accessorKey }>
                  { column.cell !== undefined ? column.cell({ row: { index }, getValue: () => row[column.accessorKey] }) : String(row[column.accessorKey]) }
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
    Flex: ({ children }: { children?: React.ReactNode }) => <div>{ children }</div>,
    IconButton: ({ icon, onClick }: { icon: { value: string }, onClick: () => void }) => <button onClick={ onClick }>{ icon.value }</button>,
    IconTextButton: ({ children, onClick }: { children?: React.ReactNode, onClick: (event: { stopPropagation: () => void }) => void }) => (
      <button onClick={ () => { onClick({ stopPropagation: () => undefined }) } }>{ children }</button>
    ),
    Accordion: ({ items }: { items: Array<{ key: string, info?: React.ReactNode, children?: React.ReactNode }> }) => (
      <div>{ items.map((item) => <div key={ item.key }>{ item.info }{ item.children }</div>) }</div>
    ),
    Modal: ({ open }: { open: boolean }) => open ? <div data-testid="add-modal" /> : null,
    Select: () => null,
    OperationalGrid
  }
})

vi.mock('../../src/modules/config/users-api-slice.gen', () => ({
  useBundleDataHubUsersCollectionQuery: () => ({ data: { items: [] } })
}))

const { WorkspaceGrid } = await import('../../src/modules/graphql/components/tabs/security-definition-tab/workspace-grid')
const { PermissionGrid } = await import('../../src/modules/graphql/components/tabs/permissions-tab/permission-grid')

afterEach(() => { cleanup() })

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
})
