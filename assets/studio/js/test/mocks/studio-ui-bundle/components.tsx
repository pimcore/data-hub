/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { createContext, useContext, useMemo, useState } from 'react'

type Props = { children?: React.ReactNode } & Record<string, any>
const passthrough = ({ children }: Props): React.JSX.Element => <div>{ children }</div>

export const Box = passthrough
export const Flex = passthrough
export const Space = passthrough
export const Content = passthrough
export const Toolbar = passthrough
export const GridContentRenderer = passthrough
export const Spin = (): React.JSX.Element => <span>spin</span>
export const Text = ({ children }: Props): React.JSX.Element => <span>{ children }</span>
export const Button = ({ children, onClick }: Props): React.JSX.Element => (
  <button
    onClick={ onClick }
    type='button'
  >{ children }</button>
)
export const IconButton = ({ onClick }: Props): React.JSX.Element => (
  <button
    onClick={ onClick }
    type='button'
  >icon</button>
)
export const IconTextButton = Button
export const ColumnPickerPopover = ({ children }: Props): React.JSX.Element => <>{ children }</>
export const ColumnPicker = (): null => null
export const Header = passthrough
export const Alert = passthrough
export const CodeEditor = ({ value }: Props): React.JSX.Element => <pre>{ value }</pre>
export const LanguageSelection = (): null => null
export const ModalTitle = passthrough
export const useAlertModal = (): { warn: () => void } => ({ warn: () => {} })

export const Modal = ({ open, title, children }: Props): React.JSX.Element | null =>
  open === true ? <div role='dialog'>{ title }{ children }</div> : null

export const ContentLayout = ({ renderTopBar, children }: Props): React.JSX.Element => (
  <div>{ renderTopBar }{ children }</div>
)

export const StackList = ({ items }: Props): React.JSX.Element => (
  <ul>
    { items.map((item: any) => (
      <li
        data-testid={ `column-${item.id}` }
        key={ item.id }
      >{ item.children }{ item.body }</li>
    )) }
  </ul>
)

// A cell is shown as "type:dataObjectType=value" so tests can read what was rendered.
export const Grid = ({ columns, data }: Props): React.JSX.Element => (
  <div data-testid='preview-grid'>
    { columns.map((column: any, index: number) => (
      <span key={ index }>{ `${column.meta.type}:${column.meta.config.dataObjectType ?? ''}=${JSON.stringify(data[0][column.accessorKey])}` }</span>
    )) }
  </div>
)

// Same contract as studio-ui-bundle's preview mode module (F2-F4).
interface Item { elementType?: string, data: { id?: string | number, fullpath?: string } }
interface PreviewModeValue { enabled: boolean, setEnabled: (enabled: boolean) => void, item: Item | null, setItem: (item: Item | null) => void }
const PreviewModeContext = createContext<PreviewModeValue | undefined>(undefined)
const inactive: PreviewModeValue = { enabled: false, setEnabled: () => {}, item: null, setItem: () => {} }

export const PreviewModeProvider = ({ children }: Props): React.JSX.Element => {
  const [enabled, setEnabled] = useState(false)
  const [item, setItem] = useState<Item | null>(null)
  const value = useMemo(() => ({ enabled, setEnabled, item, setItem }), [enabled, item])
  return <PreviewModeContext.Provider value={ value }>{ children }</PreviewModeContext.Provider>
}

export const usePreviewMode = (): PreviewModeValue => {
  const context = useContext(PreviewModeContext)
  if (context === undefined) throw new Error('usePreviewMode must be used within a PreviewModeProvider')
  return context
}

export const PreviewModeSwitch = (): React.JSX.Element => {
  const { enabled, setEnabled } = usePreviewMode()
  return (
    <input
      aria-label='preview-mode.switch'
      checked={ enabled }
      onChange={ (event) => { setEnabled(event.target.checked) } }
      role='switch'
      type='checkbox'
    />
  )
}

export const PreviewModeBar = ({ allowedClasses }: { allowedClasses?: string[] }): React.JSX.Element => {
  const { item, setItem } = usePreviewMode()
  return (
    <div
      data-classes={ allowedClasses?.join(',') }
      data-testid='preview-bar'
    >
      <button
        onClick={ () => { setItem({ elementType: 'data-object', data: { id: 9, fullpath: '/picked' } }) } }
        type='button'
      >preview-mode.preview-item</button>
      <span>{ item?.data.id === undefined ? 'preview-mode.no-item' : `ID: ${item.data.id}` }</span>
    </div>
  )
}

export const ColumnPreview = ({ source, children }: { source?: any, children?: () => React.ReactNode }): React.JSX.Element | null => {
  const { enabled } = useContext(PreviewModeContext) ?? inactive
  if (!enabled) return null
  if (children !== undefined) return <>{ children() }</>
  if (source?.isLoading === true) return <span>column-preview-loading</span>
  return <>{ source?.value ?? null }</>
}
