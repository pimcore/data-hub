/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { useMemo } from 'react'
import { useClassDefinitionGetLayoutByIdQuery } from '@pimcore/studio-ui-bundle/api/class-definition'
import { reduce, buildTree } from '@pimcore/studio-ui-bundle/modules/field-definitions'
import { type DynamicTypeFieldDefinitionRegistry } from '@pimcore/studio-ui-bundle/modules/field-definitions'
import { useInjection, serviceIds, useTranslation } from '@pimcore/studio-ui-bundle/app'
import { Icon } from '@pimcore/studio-ui-bundle/components'
import { isNil } from 'lodash'
import { systemColumnDefinitions, SYSTEM_COLUMN_ICON } from '../definitions/system-column-definitions'

interface UseClassAttributesTreeProps {
  classId: string
  enabled: boolean
}

interface TreeNode {
  key: React.Key
  title?: React.ReactNode | ((data: any) => React.ReactNode)
  icon?: React.ReactNode
  isLeaf?: boolean
  className?: string
  iconProps?: any
  attribute?: string
  dataType?: string
  children?: TreeNode[]
}

interface UseClassAttributesTreeReturn {
  classAttributesTree: TreeNode[]
  isLoading: boolean
}

export const useClassAttributesTree = ({
  classId,
  enabled
}: UseClassAttributesTreeProps): UseClassAttributesTreeReturn => {
  const fieldDefinitionRegistry = useInjection<DynamicTypeFieldDefinitionRegistry>(serviceIds['DynamicTypes/FieldDefinitionRegistry'])
  const { t } = useTranslation()

  const { data: classLayout, isLoading, isFetching } = useClassDefinitionGetLayoutByIdQuery(
    { id: classId },
    { skip: !enabled }
  )

  const classAttributesTree = useMemo(() => {
    const buildDataObjectColumns = (): TreeNode['children'] => {
      if (isNil(classLayout)) return []

      const reduced = reduce({ layout: classLayout })
      if (reduced?.structure === undefined) return []

      const { structure, fieldDefinitions } = reduced
      const tree = buildTree({
        structure,
        fieldDefinitions,
        itemCallback: ({ fieldDefinition, initialTreeItem }) => {
          const dynType = fieldDefinitionRegistry.getDynamicType(fieldDefinition.fieldtype, false)

          const { icon: _icon, ...restTreeItem } = initialTreeItem

          return {
            ...restTreeItem,
            className: 'ant-tree-node--has-drag-and-drop',
            icon: initialTreeItem.icon,
            dataType: fieldDefinition.fieldtype,
            iconProps: dynType !== undefined ? dynType.getIcon() : { value: 'field' }
          }
        }
      })

      return (tree?.children ?? []) as TreeNode[]
    }

    const buildSystemColumns = (): TreeNode => ({
      key: 'system-columns',
      title: t('data-hub.schema.system-columns'),
      isLeaf: false,
      icon: <Icon { ...SYSTEM_COLUMN_ICON } />,
      iconProps: SYSTEM_COLUMN_ICON,
      children: systemColumnDefinitions.map(({ key, attribute, translationKey, iconProps }) => ({
        key,
        title: t(translationKey),
        isLeaf: true,
        dataType: 'system',
        icon: <Icon { ...iconProps } />,
        iconProps,
        className: 'ant-tree-node--has-drag-and-drop',
        attribute
      }))
    })

    try {
      const dataObjectColumnsData = buildDataObjectColumns()
      const dataObjectColumns = {
        key: 'object-columns',
        title: t('data-hub.schema.data-object-columns'),
        isLeaf: false,
        icon: <Icon value="data-object" />,
        iconProps: { value: 'data-object' },
        children: dataObjectColumnsData
      }

      return [dataObjectColumns, buildSystemColumns()]
    } catch (error) {
      console.error('Error building class attributes tree:', error)
      return []
    }
  }, [classLayout])

  return {
    classAttributesTree,
    isLoading: isLoading || isFetching
  }
}
