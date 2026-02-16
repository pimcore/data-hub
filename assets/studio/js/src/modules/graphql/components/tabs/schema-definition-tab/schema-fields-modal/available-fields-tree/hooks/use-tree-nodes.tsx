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
import { Icon } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { isNil } from 'lodash'
import { type DynamicTypeOperatorRegistry } from '../../../../../../../operators/dynamic-type-operator-registry'
import { type DynamicTypeFieldDefinitionRegistry } from '@pimcore/studio-ui-bundle/modules/field-definitions'
import { type InternalTreeNode, createTreeItem } from '../tree-item/tree-item'
import { systemColumnLookup } from '../../definitions/system-column-definitions'
import { useOperator } from '../../../../../../../operators/hooks/use-operator'

export interface TreeNodeData {
  key: string
  title: React.ReactNode
  icon?: React.ReactNode
  iconProps?: any
  children?: TreeNodeData[]
  className?: string
  actions?: Array<{ key: string, icon: string }>
  itemData: InternalTreeNode
}

interface UseTreeNodesProps {
  items: InternalTreeNode[]
  operatorRegistry: DynamicTypeOperatorRegistry
  fieldDefinitionRegistry: DynamicTypeFieldDefinitionRegistry
}

interface NodeDisplay {
  icon?: React.ReactNode
  iconProps?: any
  title: React.ReactNode
}

export const useTreeNodes = ({
  items,
  operatorRegistry,
  fieldDefinitionRegistry
}: UseTreeNodesProps): TreeNodeData[] => {
  const { t } = useTranslation()
  const { getLocalizedName, getIcon } = useOperator()

  return useMemo(() => {
    const buildOperatorDisplay = (item: InternalTreeNode): NodeDisplay => {
      const operatorType = operatorRegistry.getDynamicType(String(item.attributes.class ?? ''), false)
      const config = { key: item.key, isOperator: true, attributes: item.attributes }

      if (operatorType === null) {
        return {
          icon: undefined,
          iconProps: undefined,
          title: String(item.attributes.label ?? '')
        }
      }

      const iconProps = getIcon(operatorType, operatorRegistry)

      return {
        icon: <Icon
          { ...iconProps }
          iconColorGroup="operator"
              />,
        iconProps,
        title: operatorType.getLabel(config, getLocalizedName(operatorType)) ?? String(item.attributes.label ?? '')
      }
    }

    const buildSystemColumnDisplay = (item: InternalTreeNode): NodeDisplay => {
      const attributeValue = item.attributes.attribute ?? ''
      const systemColumn = systemColumnLookup.get(attributeValue)

      if (systemColumn === undefined) {
        return { icon: undefined, iconProps: undefined, title: undefined }
      }

      return {
        icon: <Icon { ...systemColumn.iconProps } />,
        iconProps: systemColumn.iconProps,
        title: t(systemColumn.translationKey)
      }
    }

    const buildFieldDefinitionDisplay = (item: InternalTreeNode): NodeDisplay => {
      const fieldDef = fieldDefinitionRegistry.getDynamicType(item.attributes.dataType ?? '', false)
      const iconProps = fieldDef?.getIcon()

      return {
        icon: isNil(iconProps)
          ? undefined
          : (
            <Icon
              { ...iconProps }
              iconColorGroup="fieldDefinition"
            />
            ),
        iconProps,
        title: item.attributes.label ?? item.attributes.attribute ?? ''
      }
    }

    const getNodeDisplay = (item: InternalTreeNode): NodeDisplay => {
      if (item.isOperator) return buildOperatorDisplay(item)
      if (item.attributes.dataType === 'system') return buildSystemColumnDisplay(item)
      return buildFieldDefinitionDisplay(item)
    }

    const buildNode = (item: InternalTreeNode): TreeNodeData => {
      const treeItem = createTreeItem(item, operatorRegistry)
      const display = getNodeDisplay(item)

      return {
        key: item.key,
        title: display.title,
        icon: display.icon,
        iconProps: display.iconProps,
        className: 'ant-tree-node--has-drag-and-drop',
        actions: treeItem.getActions(),
        itemData: item,
        children: item.attributes.children?.map(buildNode)
      }
    }

    return items.map(buildNode)
  }, [items, operatorRegistry, fieldDefinitionRegistry])
}
