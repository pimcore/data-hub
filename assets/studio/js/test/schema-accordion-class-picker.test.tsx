/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React from 'react'
import { render, screen, fireEvent } from '@testing-library/react'
import { SchemaAccordion } from '../src/modules/graphql/components/tabs/schema-definition-tab/schema-accordion'

// The mocked useClassDefinitions offers Car, Category, Customer and News
const renderAccordion = (value = [{ id: 'Customer', entity: 'Customer' }]): jest.Mock => {
  const onChange = jest.fn()
  render(
    <SchemaAccordion
      onChange={ onChange }
      type='query'
      value={ value }
    />
  )
  fireEvent.click(screen.getByRole('button', { name: 'add' }))
  return onChange
}

const search = (text: string): void => {
  fireEvent.change(screen.getByRole('searchbox', { name: 'data-hub.schema.search-classes' }), { target: { value: text } })
}

const checkbox = (name: string): HTMLElement => screen.getByRole('checkbox', { name })

describe('SchemaAccordion class picker', () => {
  it('selects all classes of the filtered list only, without the already added ones', () => {
    const onChange = renderAccordion()

    search('c')
    expect(screen.queryByRole('checkbox', { name: 'News' })).not.toBeInTheDocument()
    fireEvent.click(screen.getByRole('button', { name: 'data-hub.schema.select-all' }))
    search('')

    expect(checkbox('Car')).toBeChecked()
    expect(checkbox('Category')).toBeChecked()
    expect(checkbox('News')).not.toBeChecked()

    fireEvent.click(screen.getByRole('button', { name: 'button.apply' }))
    expect(onChange).toHaveBeenCalledWith([
      { id: 'Customer', entity: 'Customer' },
      { id: 'Car', entity: 'Car' },
      { id: 'Category', entity: 'Category' }
    ])
  })

  it('shows classes already in the schema as checked and disabled', () => {
    renderAccordion()

    expect(checkbox('Customer')).toBeChecked()
    expect(checkbox('Customer')).toBeDisabled()
    expect(checkbox('Car')).toBeEnabled()

    fireEvent.click(screen.getByRole('button', { name: 'data-hub.schema.deselect-all' }))
    expect(checkbox('Customer')).toBeChecked()
  })

  it('shows an empty state when the search matches no class', () => {
    renderAccordion()

    search('nothing-matches')
    expect(screen.queryAllByRole('checkbox')).toHaveLength(0)
    expect(screen.getByText('data-hub.schema.no-classes-found')).toBeInTheDocument()
  })

  it('resets the selection and the search when the modal is closed', () => {
    const onChange = renderAccordion()

    search('car')
    fireEvent.click(checkbox('Car'))
    fireEvent.click(screen.getByRole('button', { name: 'button.cancel' }))
    expect(screen.queryByRole('dialog')).not.toBeInTheDocument()

    fireEvent.click(screen.getByRole('button', { name: 'add' }))
    expect(screen.getByRole('searchbox', { name: 'data-hub.schema.search-classes' })).toHaveValue('')
    expect(checkbox('Car')).not.toBeChecked()
    expect(checkbox('News')).toBeInTheDocument()

    fireEvent.click(screen.getByRole('button', { name: 'button.apply' }))
    expect(onChange).not.toHaveBeenCalled()
  })
})
