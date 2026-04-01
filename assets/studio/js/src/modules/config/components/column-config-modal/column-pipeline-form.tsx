/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { useEffect, useState } from 'react'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { Form, Input, Pipeline, PipelineConfigProvider, Tabs } from '@pimcore/studio-ui-bundle/components'
import { isEqual } from 'lodash'
import { type AdvancedEditorColumn } from './types'
import { ColumnPreview } from './column-preview'

export interface ColumnPipelineFormProps {
  column?: AdvancedEditorColumn
  entity?: string
  config?: Record<string, any>
  objectId?: number | null
  value?: Record<string, any>
  onChange?: (value: Record<string, any>) => void
  /** Service ID of the DynamicTypePipelineRegistry to use for source fields. */
  sourceFieldsRegistryId: string
  /** Service ID of the DynamicTypePipelineRegistry to use for transformers. */
  transformersRegistryId: string
}

export const ColumnPipelineForm = ({
  column,
  entity,
  config,
  objectId,
  value,
  onChange,
  sourceFieldsRegistryId,
  transformersRegistryId
}: ColumnPipelineFormProps): React.JSX.Element => {
  const { t } = useTranslation()
  const [form] = Form.useForm()
  const [liveValue, setLiveValue] = useState<Record<string, any>>(value ?? {})

  useEffect(() => {
    form.setFieldValue('value', value ?? {})
  }, [value])

  const onValuesChange = (changedValues: Record<string, any>): void => {
    const newPipelineValue = form.getFieldValue('value') as Record<string, any>
    if (newPipelineValue !== undefined && !isEqual(liveValue, newPipelineValue)) {
      setLiveValue(newPipelineValue)
      onChange?.(newPipelineValue)
    }
  }

  return (
    <Form
      form={ form }
      initialValues={ { value: value ?? {} } }
      layout='vertical'
      onValuesChange={ onValuesChange }
    >
      <PipelineConfigProvider initialConfig={ config ?? {} }>
        <Form.Item name='value'>
          <Pipeline
            items={ [
              {
                id: 'fields',
                component: (
                  <Pipeline.CustomItem>
                    <Tabs
                      items={ [
                        {
                          key: 'title',
                          label: t('data-hub.column-config-modal.pipeline.title'),
                          forceRender: true,
                          children: (
                            <Form.Item
                              label={ t('data-hub.column-config-modal.pipeline.title') }
                              name='title'
                            >
                              <Input />
                            </Form.Item>
                          )
                        },
                        {
                          key: 'sourceFields',
                          label: t('grid.advanced-column.advancedColumns'),
                          forceRender: true,
                          children: (
                            <Pipeline.DynamicGroupItem
                              dynamicTypeRegistryId={ sourceFieldsRegistryId }
                              id='sourceFields'
                              translationKeyPrefix='data-hub.column-config-modal.pipeline'
                            />
                          )
                        },
                        {
                          key: 'transformers',
                          label: t('grid.advanced-column.transformers'),
                          forceRender: true,
                          children: (
                            <Pipeline.DynamicGroupItem
                              dynamicTypeRegistryId={ transformersRegistryId }
                              id='transformers'
                              translationKeyPrefix='data-hub.column-config-modal.pipeline'
                            />
                          )
                        }
                      ] }
                    />
                  </Pipeline.CustomItem>
                )
              },
              {
                id: 'preview',
                component: (
                  <Pipeline.CustomItem>
                    { column !== undefined && (
                      <ColumnPreview
                        column={ column }
                        objectId={ objectId ?? null }
                        pipelineValue={ liveValue }
                      />
                    ) }
                  </Pipeline.CustomItem>
                )
              }
            ] }
            value={ value ?? {} }
          />
        </Form.Item>
      </PipelineConfigProvider>
    </Form>
  )
}
