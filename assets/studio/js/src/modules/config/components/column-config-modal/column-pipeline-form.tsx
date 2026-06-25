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
import { Box, Form, Input, Pipeline, PipelineConfigProvider, SplitLayout, Tabs } from '@pimcore/studio-ui-bundle/components'
import { isEqual } from 'lodash'
import { type AdvancedEditorColumn } from './types'
import { ColumnPreview } from './column-preview'
import { useCompactLayout } from '../migration-modal'

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
  const { compact } = useCompactLayout()
  const [liveValue, setLiveValue] = useState<Record<string, any>>(value ?? {})

  const sourceFieldsGroup = (
    <Pipeline.DynamicGroupItem
      dynamicTypeRegistryId={ sourceFieldsRegistryId }
      id='sourceFields'
      showTitle={ !compact }
      translationKeyPrefix='data-hub.column-config-modal.pipeline'
    />
  )

  const transformersGroup = (
    <Pipeline.DynamicGroupItem
      dynamicTypeRegistryId={ transformersRegistryId }
      id='transformers'
      showTitle={ !compact }
      translationKeyPrefix='data-hub.column-config-modal.pipeline'
    />
  )

  const fieldsLayout = compact
    ? (
      <Tabs
        items={ [
          {
            key: 'sourceFields',
            label: t('data-hub.column-config-modal.pipeline.sourceFields'),
            forceRender: true,
            children: sourceFieldsGroup
          },
          {
            key: 'transformers',
            label: t('data-hub.column-config-modal.pipeline.transformers'),
            forceRender: true,
            children: transformersGroup
          }
        ] }
      />
      )
    : (
      <SplitLayout
        leftItem={ {
          children: sourceFieldsGroup,
          size: 50
        } }
        rightItem={ {
          children: transformersGroup,
          size: 50
        } }
        withDivider
      />
      )

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
                id: 'title',
                component: (
                  <Pipeline.CustomItem>
                    <Box padding={ { top: 'mini', bottom: 'mini', x: 'none' } }>
                      <Form.Item name='title'>
                        <Input
                          placeholder={ t('data-hub.column-config-modal.pipeline.title') }
                          style={ { maxWidth: '100%' } }
                        />
                      </Form.Item>
                    </Box>
                  </Pipeline.CustomItem>
                )
              },
              {
                id: 'fields',
                component: (
                  <Pipeline.CustomItem>
                    { fieldsLayout }
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
