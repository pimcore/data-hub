/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { useState } from 'react'
import { Form, Compact, Select, TextArea, Switch, IconButton, Space, Text, Flex, FormKit, Tooltip, OperationalGrid, IconTextButton, Checkbox, Accordion } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { useFieldWidth } from '@pimcore/studio-ui-bundle/modules/element'
import { createColumnHelper } from '@tanstack/react-table'

interface SecurityDefinitionTabProps {
  onFormChange?: () => void
}

interface Workspace {
  path: string
  create: boolean
  read: boolean
  update: boolean
  delete: boolean
}

export const SecurityDefinitionTab = ({ onFormChange }: SecurityDefinitionTabProps): React.JSX.Element => {
  const { t } = useTranslation()
  const form = Form.useFormInstance()
  const fieldWidth = useFieldWidth()

  const generateApiKey = (): void => {
    const currentValue = form.getFieldValue(['security', 'apikey']) || ''
    const newKey = generateRandomKey()
    const newValue = currentValue ? `${currentValue}\n${newKey}` : newKey
    
    form.setFieldsValue({
      security: {
        apikey: newValue
      }
    })
    
    // Manually trigger form change
    if (onFormChange) {
      onFormChange()
    }
  }

  const generateRandomKey = (): string => {
    return Array.from(crypto.getRandomValues(new Uint8Array(16)))
      .map(b => b.toString(16).padStart(2, '0'))
      .join('')
  }

  const methodOptions = [
    {
      value: 'datahub_apikey',
      label: t('data-hub.security.method.apikey')
    }
  ]

  return (
    <FormKit.Panel contentPadding="extra-small">
        <FormKit.Panel
          contentPadding="extra-small"
          title={ t('data-hub.security.authentication') }
        >
          <Form.Item
            label={ t('data-hub.security.method') }
            name={ ['security', 'method'] }
            initialValue="datahub_apikey"
          >
            <Select
              options={ methodOptions }
            />
          </Form.Item>

          <Form.Item
            label={ t('data-hub.security.apikey') }
            help={ t('data-hub.security.apikey-description') }
          >
            <Flex gap={ 8 } align="flex-start">
              <Form.Item
                name={ ['security', 'apikey'] }
                noStyle
              >
                <TextArea
                  autoSize={ { minRows: 4, maxRows: 10 } }
                />
              </Form.Item>
              <Tooltip title={ t('data-hub.security.generate-apikey') }>
                <IconButton
                  icon={ {value: 'asset'} }
                  onClick={ generateApiKey }
                  type="default"
                />
              </Tooltip>
            </Flex>
          </Form.Item>

          <Form.Item
            name={ ['security', 'skipPermissionCheck'] }
            valuePropName="checked"
          >
            <Switch labelRight={ t('data-hub.security.skip-permission-check') } />
          </Form.Item>

          <Form.Item
            name={ ['security', 'disableIntrospection'] }
            valuePropName="checked"
            help={ t('data-hub.security.introspection-description') }
          >
            <Switch labelRight={ t('data-hub.security.disable-introspection') } />
          </Form.Item>
        </FormKit.Panel>

        <FormKit.Panel
          contentPadding="extra-small"
          title={ t('data-hub.workspaces.title') }
        >
          <Flex
            gap="small"
            vertical
          >
            <Form.Item
              name={ ['workspaces', 'documents'] }
              noStyle
            >
              <OperationalGrid
                autoWidth
                columns={ getWorkspaceColumns('documents') }
                value={ form.getFieldValue(['workspaces', 'documents']) || [] }
              >
                <OperationalGrid.Operations>
                  {(operations) => (
                    <Accordion
                      activeKey="documents"
                      bordered
                      collapsible="icon"
                      items={ [renderWorkspaceAccordion('documents', operations)] }
                      size="small"
                      table
                    />
                  )}
                </OperationalGrid.Operations>
              </OperationalGrid>
            </Form.Item>

            <Form.Item
              name={ ['workspaces', 'assets'] }
              noStyle
            >
              <OperationalGrid
                autoWidth
                columns={ getWorkspaceColumns('assets') }
                value={ form.getFieldValue(['workspaces', 'assets']) || [] }
              >
                <OperationalGrid.Operations>
                  {(operations) => (
                    <Accordion
                      activeKey="assets"
                      bordered
                      collapsible="icon"
                      items={ [renderWorkspaceAccordion('assets', operations)] }
                      size="small"
                      table
                    />
                  )}
                </OperationalGrid.Operations>
              </OperationalGrid>
            </Form.Item>

            <Form.Item
              name={ ['workspaces', 'objects'] }
              noStyle
            >
              <OperationalGrid
                autoWidth
                columns={ getWorkspaceColumns('objects') }
                value={ form.getFieldValue(['workspaces', 'objects']) || [] }
              >
                <OperationalGrid.Operations>
                  {(operations) => (
                    <Accordion
                      activeKey="objects"
                      bordered
                      collapsible="icon"
                      items={ [renderWorkspaceAccordion('objects', operations)] }
                      size="small"
                      table
                    />
                  )}
                </OperationalGrid.Operations>
              </OperationalGrid>
            </Form.Item>
          </Flex>
        </FormKit.Panel>
    </FormKit.Panel>
  )

  function renderWorkspaceAccordion(type: 'documents' | 'assets' | 'objects', operations: any) {
    const fieldName = ['workspaces', type]
    const workspaces = form.getFieldValue(fieldName) || []
    
    return {
      key: type,
      id: type,
      title: <>{t(`data-hub.workspaces.${type}`)}</>,
      info: (
        <IconTextButton
          icon={ { value: 'add-find' } }
          onClick={ (e) => {
            e.stopPropagation()
            operations.addRow({
              path: '',
              create: false,
              read: true,
              update: false,
              delete: false
            })
          } }
        >
          {t('data-hub.workspaces.add')}
        </IconTextButton>
      ),
      children: (
        <OperationalGrid.Grid />
      )
    }
  }

  function getWorkspaceColumns(type: 'documents' | 'assets' | 'objects') {
    const columnHelper = createColumnHelper<Workspace>()
    
    // Map workspace type to link type
    const linkType = type === 'documents' ? 'document-link' : type === 'assets' ? 'asset-link' : 'object-link'
    const fieldName = ['workspaces', type]

    return [
      columnHelper.accessor('path', {
        header: t('data-hub.workspaces.path'),
        size: 300,
        meta: {
          editable: true,
          type: linkType,
          autoWidth: true
        }
      }),
      columnHelper.accessor('create', {
        header: t('data-hub.workspaces.create'),
        size: 80,
        meta: {
          type: 'checkbox',
          editable: true,
          config: {
            align: 'center'
          }
        }
      }),
      columnHelper.accessor('read', {
        header: t('data-hub.workspaces.read'),
        size: 80,
        meta: {
          type: 'checkbox',
          editable: true,
          config: {
            align: 'center'
          }
        }
      }),
      columnHelper.accessor('update', {
        header: t('data-hub.workspaces.update'),
        size: 80,
        meta: {
          type: 'checkbox',
          editable: true,
          config: {
            align: 'center'
          }
        }
      }),
      columnHelper.accessor('delete', {
        header: t('data-hub.workspaces.delete'),
        size: 80,
        meta: {
          type: 'checkbox',
          editable: true,
          config: {
            align: 'center'
          }
        }
      }),
      {
        id: 'actions',
        header: '',
        size: 60,
        cell: (info: any) => (
          <Flex
            align="center"
            justify="center"
          >
            <IconButton
              icon={ { value: 'trash' } }
              onClick={ () => {
                const currentWorkspaces = form.getFieldValue(fieldName) || []
                const newData = [...currentWorkspaces]
                newData.splice(info.row.index as number, 1)
                form.setFieldValue(fieldName, newData)
                if (onFormChange) {
                  onFormChange()
                }
              } }
              type="link"
            />
          </Flex>
        ),
        enableResizing: false,
        enableSorting: false
      }
    ]
  }
}
