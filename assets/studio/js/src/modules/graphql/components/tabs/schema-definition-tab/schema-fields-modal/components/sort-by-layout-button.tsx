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
import { Button } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { isEqual, isNil } from 'lodash'
import { type QueryEntityConfig } from '../types'
import { useClassAttributesTree } from '../hooks/use-class-attributes-tree'
import { collectAttributeOrder, sortColumnsByLayout } from '../hooks/sort-columns-by-layout'

interface SortByLayoutButtonProps {
  classId: string
  enabled: boolean
  entityConfig?: QueryEntityConfig
  onEntityConfigChange: (config: QueryEntityConfig) => void
}

export const SortByLayoutButton = ({
  classId,
  enabled,
  entityConfig,
  onEntityConfigChange
}: SortByLayoutButtonProps): React.JSX.Element => {
  const { t } = useTranslation()
  const { classAttributesTree, isLoading } = useClassAttributesTree({ classId, enabled })

  const columns = useMemo(() => entityConfig?.columnConfig?.columns ?? [], [entityConfig])

  const sortedColumns = useMemo(
    () => sortColumnsByLayout(columns, collectAttributeOrder(classAttributesTree)),
    [columns, classAttributesTree]
  )

  const handleSort = (): void => {
    if (isNil(entityConfig)) return

    onEntityConfigChange({
      ...entityConfig,
      columnConfig: {
        ...entityConfig.columnConfig,
        columns: sortedColumns
      }
    })
  }

  return (
    <Button
      disabled={ isLoading || isNil(entityConfig) || isEqual(sortedColumns, columns) }
      onClick={ handleSort }
    >
      {t('data-hub.schema.sort-by-class-layout')}
    </Button>
  )
}
