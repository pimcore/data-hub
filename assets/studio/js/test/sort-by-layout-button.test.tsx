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
import { SortByLayoutButton } from '../src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/components/sort-by-layout-button'
import { type QueryEntityConfig } from '../src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/types'

const tree = { classAttributesTree: [] as any[], isLoading: false }

jest.mock('../src/modules/graphql/components/tabs/schema-definition-tab/schema-fields-modal/hooks/use-class-attributes-tree', () => ({
  useClassAttributesTree: () => tree
}))

jest.mock('@pimcore/studio-ui-bundle/components', () => ({
  ...jest.requireActual('./mocks/studio-ui-bundle/components'),
  Button: ({ children, onClick, disabled }: any) => (
    <button
      disabled={ disabled }
      onClick={ onClick }
      type='button'
    >{ children }</button>
  )
}))

const layout = [{
  key: 'object-columns',
  children: [
    { key: 'name', attribute: 'name', isFieldDefinition: true },
    { key: 'price', attribute: 'price', isFieldDefinition: true }
  ]
}]

const entityConfig = {
  id: 'Product',
  name: 'Product',
  columnConfig: {
    columns: [
      { isOperator: false, attributes: { attribute: 'price' } },
      { isOperator: false, attributes: { attribute: 'name' } }
    ]
  }
} as unknown as QueryEntityConfig

const renderButton = (onEntityConfigChange = jest.fn()): HTMLElement => {
  render(
    <SortByLayoutButton
      classId='Product'
      enabled
      entityConfig={ entityConfig }
      onEntityConfigChange={ onEntityConfigChange }
    />
  )
  return screen.getByRole('button', { name: 'data-hub.schema.sort-by-class-layout' })
}

describe('sort by layout button', () => {
  test('is disabled while the class and brick layouts load', () => {
    tree.classAttributesTree = layout
    tree.isLoading = true
    expect(renderButton()).toBeDisabled()
  })

  test('sorts the columns once the layouts are loaded', () => {
    tree.classAttributesTree = layout
    tree.isLoading = false
    const onEntityConfigChange = jest.fn()
    const button = renderButton(onEntityConfigChange)

    expect(button).toBeEnabled()
    fireEvent.click(button)
    expect(onEntityConfigChange.mock.calls[0][0].columnConfig.columns.map((c: any) => c.attributes.attribute)).toEqual(['name', 'price'])
  })
})
