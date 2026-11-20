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
import { render, screen } from '@testing-library/react'
import { ColumnPipelineForm } from '../src/modules/config/components/column-config-modal/column-pipeline-form'

// a form that shows whether it is handed the read-only state; the pipeline itself is not under test
jest.mock('@pimcore/studio-ui-bundle/components', () => {
  const passthrough = ({ children }: { children?: React.ReactNode }): React.JSX.Element => <>{ children }</>
  const Form = ({ disabled, children }: { disabled?: boolean, children?: React.ReactNode }): React.JSX.Element => (
    <form
      aria-label='pipeline'
      data-disabled={ String(disabled) }
    >{ children }</form>
  )
  Form.useForm = () => [{ setFieldValue: () => {}, getFieldValue: () => undefined }]
  Form.Item = passthrough
  const nothing = (): null => null
  const Pipeline = (): null => null
  Pipeline.DynamicGroupItem = nothing
  Pipeline.CustomItem = passthrough
  return {
    ...jest.requireActual('@pimcore/studio-ui-bundle/components'),
    Form,
    Pipeline,
    PipelineConfigProvider: passthrough,
    SplitLayout: (): null => null,
    Tabs: (): null => null,
    Input: (): null => null
  }
})

jest.mock('../src/modules/config/components/column-config-modal/column-preview', () => ({
  ColumnPreview: () => null
}))

const form = (disabled?: boolean): React.JSX.Element => (
  <ColumnPipelineForm
    disabled={ disabled }
    sourceFieldsRegistryId='source'
    transformersRegistryId='transformers'
  />
)

describe('column pipeline form', () => {
  test('disabled: the settings form is disabled', () => {
    render(form(true))
    expect(screen.getByLabelText('pipeline').dataset.disabled).toBe('true')
  })

  test('editable by default', () => {
    render(form())
    expect(screen.getByLabelText('pipeline').dataset.disabled).toBe('undefined')
  })
})
