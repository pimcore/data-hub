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
import { Icon, Content } from '@pimcore/studio-ui-bundle/components'
import { useTranslation, useInjection } from '@pimcore/studio-ui-bundle/app'
import { type DynamicTypeOperatorRegistry } from '../../../../../../../modules/operators/dynamic-type-operator-registry'
import { useOperator } from '../../../../../../../modules/operators/hooks/use-operator'
import { useOperatorGroups } from '../../../../../../../modules/operators/hooks/use-operator-groups'
import { ClassAttributesSidebar } from '../components/class-attributes-sidebar'
import { OperatorsSidebar } from '../components/operators-sidebar'

interface UseSidebarEntriesProps {
  classId: string
  enabled: boolean
  operatorRegistryServiceId: string
  gridContainerClassName: string
}

interface SidebarEntry {
  key: string
  icon: React.JSX.Element
  tooltip: string
  component: React.JSX.Element
}

export const useSidebarEntries = ({
  classId,
  enabled,
  operatorRegistryServiceId,
  gridContainerClassName
}: UseSidebarEntriesProps): SidebarEntry[] => {
  const { t } = useTranslation()
  const operatorRegistry = useInjection<DynamicTypeOperatorRegistry>(operatorRegistryServiceId)
  const { getGroup } = useOperator()
  const { getGroupIcon } = useOperatorGroups(operatorRegistry)

  return useMemo(() => {
    const entries: SidebarEntry[] = []

    // Add class attributes entry
    entries.push({
      key: 'class-attributes',
      icon: <Icon value="data-object" />,
      tooltip: t('data-hub.schema.class-attributes'),
      component: (
        <ClassAttributesSidebar
          classId={ classId }
          enabled={ enabled }
        />
      )
    })

    // Organize operators by group
    const operators = operatorRegistry.getDynamicTypes()
    const groups = new Map<string, {
      groupName: string
      priority: number
    }>()

    operators.forEach(operator => {
      const groupName = operator.getGroup()
      const { group: translatedGroup } = getGroup(operator)
      const groupConfig = operatorRegistry.getGroupConfig(groupName)
      const priority = groupConfig?.priority ?? 999

      if (!groups.has(translatedGroup)) {
        groups.set(translatedGroup, { groupName, priority })
      }
    })

    // Add operator group entries
    Array.from(groups.entries())
      .sort(([, a], [, b]) => a.priority - b.priority)
      .forEach(([groupKey, groupData]) => {
        const groupIcon = getGroupIcon(groupData.groupName)

        entries.push({
          key: groupKey,
          icon: <Icon { ...groupIcon } />,
          tooltip: t(groupKey),
          component: (
            <Content>
              <OperatorsSidebar
                gridContainerClassName={ gridContainerClassName }
                groupName={ groupData.groupName }
                operatorRegistryServiceId={ operatorRegistryServiceId }
                translatedGroupName={ groupKey }
              />
            </Content>
          )
        })
      })

    return entries
  }, [classId, enabled, operatorRegistryServiceId, gridContainerClassName])
}
