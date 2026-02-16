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
import { Content, ContentLayout, TreeElement, SidebarTitle, Box, SearchInput } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { DraggableTreeTitle } from './draggable-tree-title'
import { type TreeNode } from '../types'
import { useClassAttributesTree } from '../hooks/use-class-attributes-tree'
import { useStyles } from './class-attributes-sidebar.styles'

interface ClassAttributesSidebarProps {
  classId: string
  enabled: boolean
}

export const ClassAttributesSidebar = ({
  classId,
  enabled
}: ClassAttributesSidebarProps): React.JSX.Element => {
  const { t } = useTranslation()
  const { styles } = useStyles()
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
          <Box padding={ 'small' }>
            <SearchInput
              onChange={ (e) => { setSearchValue(e.target.value) } }
              placeholder={ t('search') }
              style={ { width: '100%', maxWidth: '100%' } }
              value={ searchValue }
              withoutAddon
            />
          </Box>
        </>
      }
    >
      <Content
        loading={ isLoading }
        padding={ { x: 'small', top: 'extra-small', bottom: 'small' } }
      >

        <TreeElement
          className={ styles.treeContainer }
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
