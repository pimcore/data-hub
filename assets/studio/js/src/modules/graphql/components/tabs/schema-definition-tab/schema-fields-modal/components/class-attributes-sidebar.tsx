/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { useCallback } from 'react'
import { Content, TreeElement, SidebarTitle, Box } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { isNil, flatMapDeep } from 'lodash'
import { DraggableTreeTitle } from './draggable-tree-title'
import { type TreeNode } from '../types'
import { useClassAttributesTree } from '../hooks/use-class-attributes-tree'

interface ClassAttributesSidebarProps {
  classId: string
  enabled: boolean
}

export const ClassAttributesSidebar = ({
  classId,
  enabled
}: ClassAttributesSidebarProps): React.JSX.Element => {
  const { t } = useTranslation()
  const { classAttributesTree, isLoading } = useClassAttributesTree({ classId, enabled })

  const collectAllKeys = (nodes: TreeNode[]): string[] => {
    return flatMapDeep(nodes, (node) => [
      ...(!isNil(node.key) ? [String(node.key)] : []),
      ...(!isNil(node.children) && Array.isArray(node.children) ? collectAllKeys(node.children as TreeNode[]) : [])
    ])
  }

  const titleRender = useCallback((node: TreeNode, initialComponent: React.ReactNode): React.JSX.Element => {
    return (
      <DraggableTreeTitle
        initialComponent={ initialComponent }
        node={ node }
      />
    )
  }, [])

  return (
    <Content loading={ isLoading }>
      <SidebarTitle withBorder>
        {t('data-hub.schema.class-attributes')}
      </SidebarTitle>

      <Box padding={ { x: 'extra-small', bottom: 'small' } }>
        <TreeElement
          defaultExpandedKeys={ collectAllKeys(classAttributesTree) }
          draggable={ false }
          selectable={ false }
          showIcon
          titleRender={ titleRender }
          treeData={ classAttributesTree }
        />
      </Box>
    </Content>
  )
}
