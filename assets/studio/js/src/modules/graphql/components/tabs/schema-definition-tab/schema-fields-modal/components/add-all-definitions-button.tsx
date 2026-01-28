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
import { IconTextButton } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { uuid } from '@pimcore/studio-ui-bundle/utils'
import { isNil } from 'lodash'
import { type QueryEntityConfig, type PersistedColumnConfig } from '../types'
import { useClassAttributesTree } from '../hooks/use-class-attributes-tree'

interface AddAllDefinitionsButtonProps {
  classId: string
  enabled: boolean
  entityConfig?: QueryEntityConfig
  onEntityConfigChange: (config: QueryEntityConfig) => void
}

export const AddAllDefinitionsButton = ({
  classId,
  enabled,
  entityConfig,
  onEntityConfigChange
}: AddAllDefinitionsButtonProps): React.JSX.Element => {
  const { t } = useTranslation()
  const { getFieldDefinitions } = useClassAttributesTree({ classId, enabled })

  const handleAddAllDefinitions = useCallback((): void => {
    if (isNil(entityConfig)) return

    const leafAttributes = getFieldDefinitions()

    const currentColumns = entityConfig.columnConfig?.columns ?? []
    const existingAttributes = new Set<string>(
      currentColumns
        .map(col => col.attributes?.attribute)
        .filter((attr): attr is string => !isNil(attr))
    )

    const newColumns: PersistedColumnConfig[] = leafAttributes
      .filter(attr => !isNil(attr.attribute) && !existingAttributes.has(attr.attribute))
      .map(attr => ({
        key: uuid(),
        isOperator: false,
        attributes: {
          attribute: attr.attribute!,
          label: String(attr.title ?? attr.attribute),
          dataType: attr.dataType ?? 'text'
        }
      }))

    if (newColumns.length > 0) {
      onEntityConfigChange({
        ...entityConfig,
        columnConfig: {
          ...entityConfig.columnConfig,
          columns: [...currentColumns, ...newColumns]
        }
      })
    }
  }, [entityConfig, getFieldDefinitions, onEntityConfigChange])

  return (
    <IconTextButton
      icon={ { value: 'plus-circle' } }
      onClick={ handleAddAllDefinitions }
    >
      {t('data-hub.schema.add-all-definitions')}
    </IconTextButton>
  )
}
