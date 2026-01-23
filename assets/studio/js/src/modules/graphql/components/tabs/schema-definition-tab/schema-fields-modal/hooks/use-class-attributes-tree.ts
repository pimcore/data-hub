/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { useMemo } from 'react'
import { useClassDefinitionGetLayoutByIdQuery } from '@pimcore/studio-ui-bundle/api/class-definition'
import { reduce, buildTree } from '@pimcore/studio-ui-bundle/modules/field-definitions'
import { type DynamicTypeFieldDefinitionRegistry } from '@pimcore/studio-ui-bundle/modules/field-definitions'
import { useInjection, serviceIds } from '@pimcore/studio-ui-bundle/app'
import { isNil } from 'lodash'

interface UseClassAttributesTreeProps {
  classId: string
  enabled: boolean
}

interface UseClassAttributesTreeReturn {
  classAttributesTree: any[]
  isLoading: boolean
}

export const useClassAttributesTree = ({
  classId,
  enabled
}: UseClassAttributesTreeProps): UseClassAttributesTreeReturn => {
  const fieldDefinitionRegistry = useInjection<DynamicTypeFieldDefinitionRegistry>(serviceIds['DynamicTypes/FieldDefinitionRegistry'])
  
  const { data: classLayout, isLoading, isFetching } = useClassDefinitionGetLayoutByIdQuery(
    { id: classId },
    { skip: !enabled }
  )

  const classAttributesTree = useMemo(() => {
    let treeData: any[] = []
    try {
      if (isNil(classLayout)) {
        treeData = []
      } else {
        // classLayout IS the layout object itself, not wrapped
        const reduced = reduce({ layout: classLayout })

        if (reduced?.structure === undefined) {
          treeData = []
        } else {
          const { structure, fieldDefinitions } = reduced

          const tree = buildTree({
            structure,
            fieldDefinitions,
            itemCallback: ({ fieldDefinition, initialTreeItem }) => {
              // Get the icon props from the field definition registry
              const dynType = fieldDefinitionRegistry.hasDynamicType(fieldDefinition.fieldtype)
                ? fieldDefinitionRegistry.getDynamicType(fieldDefinition.fieldtype)
                : undefined
              
              // Destructure to exclude the icon (React element) from initialTreeItem
              // The icon from buildTree is a React element which can cause DnD issues
              const { icon: _icon, ...restTreeItem } = initialTreeItem
              
              return {
                ...restTreeItem,
                className: 'ant-tree-node--has-drag-and-drop',
                // Re-add the icon from initialTreeItem (it's already correct for display)
                icon: initialTreeItem.icon,
                // Add dataType from fieldDefinition for use in drag data
                dataType: fieldDefinition.fieldtype,
                // Add serializable icon props for drag overlay (NOT React element)
                iconProps: dynType !== undefined ? dynType.getIcon() : { value: 'field' }
              }
            }
          })

          // buildTree returns a single root node object, not an array
          // We want to show the children of the root node
          if (!isNil(tree?.children) && Array.isArray(tree.children)) {
            treeData = tree.children
          } else {
            treeData = []
          }
        }
      }
    } catch (error) {
      console.error('Error building class attributes tree:', error)
      treeData = []
    }
    return treeData
  }, [classLayout, fieldDefinitionRegistry])

  return {
    classAttributesTree,
    isLoading: isLoading || isFetching
  }
}
