/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React, { useCallback, useState } from 'react'
import { Content, ContentLayout, TreeElement, SidebarTitle, Box, SearchInput, Divider } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
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
  const [searchValue, setSearchValue] = useState('')
  const { filteredTree, expandedKeys, isLoading } = useClassAttributesTree({
    classId,
    enabled,
    searchValue
  })

  const titleRender = useCallback((node: TreeNode, initialComponent: React.ReactNode): React.JSX.Element => {
    return (
      <DraggableTreeTitle
        initialComponent={ initialComponent }
        node={ node }
      />
    )
  }, [])

  return (
    <ContentLayout
      renderTopBar={
        <>
          <SidebarTitle withBorder>
            {t('data-hub.schema.class-attributes')}
          </SidebarTitle>
          <Box padding={ { x: 'small', y: 'extra-small' } }>
            <SearchInput
              onChange={ (e) => { setSearchValue(e.target.value) } }
              placeholder={ t('search') }
              style={ { width: '100%', maxWidth: '100%' } }
              value={ searchValue }
              withoutAddon
            />
          </Box>
          <Divider
            size="none"
            theme="secondary"
          />
        </>
      }
    >
      <Content
        loading={ isLoading }
        padded
      >

        <TreeElement
          defaultExpandedKeys={ expandedKeys }
          draggable={ false }
          selectable={ false }
          showIcon
          titleRender={ titleRender }
          treeData={ filteredTree }
        />
      </Content>
    </ContentLayout>
  )
}
