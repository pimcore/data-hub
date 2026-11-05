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
import { useLanguageSelection } from './modules-data-object'

type Props = Record<string, any>
type PropsWithChildren = { children?: React.ReactNode } & Props
const passthrough = ({ children, 'data-testid': testId }: PropsWithChildren): React.JSX.Element => <div data-testid={ testId }>{ children }</div>

export const Box = passthrough
export const Flex = passthrough
export const Space = passthrough
export const Content = passthrough
export const Toolbar = passthrough
export const GridContentRenderer = passthrough
export const Spin = (): React.JSX.Element => <span>spin</span>
export const Text = ({ children }: PropsWithChildren): React.JSX.Element => <span>{ children }</span>
export const Button = ({ children, onClick }: PropsWithChildren): React.JSX.Element => (
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
export const ColumnPickerPopover = ({ children }: PropsWithChildren): React.JSX.Element => <>{ children }</>
export const ColumnPicker = (): null => null
export const Header = passthrough
export const Alert = passthrough
export const CodeEditor = ({ value }: Props): React.JSX.Element => <pre>{ value }</pre>
export const LanguageSelection = (): null => null
export const ModalTitle = passthrough
export const useAlertModal = (): { warn: () => void } => ({ warn: () => {} })

export const Modal = ({ open, title, children }: PropsWithChildren): React.JSX.Element | null =>
  open === true ? <dialog open>{ title }{ children }</dialog> : null

export const ContentLayout = ({ renderTopBar, children }: PropsWithChildren): React.JSX.Element => (
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
    { columns.map((column: any) => (
      <span key={ column.accessorKey }>{ `${column.meta.type}:${column.meta.config.dataObjectType ?? ''}=${JSON.stringify(data[0][column.accessorKey])}` }</span>
    )) }
  </div>
)

// Same contract as studio-ui-bundle's preview mode module (F2-F4, F8).
interface Item { elementType?: string, data: { id?: string | number, fullpath?: string } }
interface PreviewModeValue {
  enabled: boolean
  setEnabled: (enabled: boolean) => void
  item: Item | null
  setItem: (item: Item | null) => void
  language: string | undefined
  setLanguage: (language: string) => void
}
const PreviewModeContext = createContext<PreviewModeValue | undefined>(undefined)
const inactive: PreviewModeValue = { enabled: false, setEnabled: () => {}, item: null, setItem: () => {}, language: undefined, setLanguage: () => {} }

export const PreviewModeProvider = ({ children, resetKey }: { children?: React.ReactNode, resetKey?: string }): React.JSX.Element => {
  const [enabled, setEnabled] = useState(false)
  const [item, setItem] = useState<Item | null>(null)
  // As the real provider: a changed resetKey clears the item (before the children render with it)
  const [seenKey, setSeenKey] = useState(resetKey)
  if (seenKey !== resetKey) {
    setSeenKey(resetKey)
    setItem(null)
  }
  const [language, setLanguage] = useState<string | undefined>(undefined)
  const value = useMemo(() => ({ enabled, setEnabled, item, setItem, language, setLanguage }), [enabled, item, language])
  return <PreviewModeContext.Provider value={ value }>{ children }</PreviewModeContext.Provider>
}

export const usePreviewMode = (): PreviewModeValue => {
  const context = useContext(PreviewModeContext)
  if (context === undefined) throw new Error('usePreviewMode must be used within a PreviewModeProvider')
  return context
}

// As the real export: undefined outside a provider is turned into an inactive preview mode
export const usePreviewModeOptional = (): PreviewModeValue => useContext(PreviewModeContext) ?? inactive

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

// One row: "Preview item" button, "ID: <id> | path" (showItem) and the language switch (showLanguageSwitch), both default on
export const PreviewModeBar = ({ allowedClasses, showItem = true, showLanguageSwitch = true }: { allowedClasses?: string[], showItem?: boolean, showLanguageSwitch?: boolean }): React.JSX.Element => {
  const { item, setItem, language, setLanguage } = usePreviewMode()
  // As the real bar (preview-mode-bar.tsx:101): without a preview language it shows the surrounding context's
  const { currentLanguage } = useLanguageSelection()
  return (
    <div
      data-classes={ allowedClasses?.join(',') }
      data-testid='preview-bar'
    >
      { showItem && (
        <>
          <button
            onClick={ () => { setItem({ elementType: 'data-object', data: { id: 9, fullpath: '/picked' } }) } }
            type='button'
          >preview-mode.preview-item</button>
          <span>{ item?.data.id === undefined ? 'preview-mode.no-item' : `ID: ${item.data.id}` }</span>
        </>
      ) }
      { showLanguageSwitch && (
        <select
          aria-label='preview-language'
          onChange={ (event) => { setLanguage(event.target.value) } }
          value={ language ?? currentLanguage }
        >
          <option value='en'>en</option>
          <option value='de'>de</option>
        </select>
      ) }
    </div>
  )
}

// The shared slot: a divider and a body row with the label "Preview" and the value
export const ColumnPreview = ({ source, children }: { source?: any, children?: () => React.ReactNode }): React.JSX.Element | null => {
  const { enabled } = useContext(PreviewModeContext) ?? inactive
  if (!enabled) return null
  let content: React.ReactNode = source?.value ?? null
  if (children !== undefined) content = children()
  else if (source?.isLoading === true) content = <span>column-preview-loading</span>
  return (
    <>
      <hr data-testid='preview-mode-column-preview-divider' />
      <div data-testid='preview-mode-column-preview-row'>
        <span>preview-mode.preview</span>
        <div>{ content }</div>
      </div>
    </>
  )
}
