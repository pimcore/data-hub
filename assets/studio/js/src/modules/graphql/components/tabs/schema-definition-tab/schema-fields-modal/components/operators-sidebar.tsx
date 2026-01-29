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
import { SidebarTitle, Box, Panel } from '@pimcore/studio-ui-bundle/components'
import { useTranslation, useInjection } from '@pimcore/studio-ui-bundle/app'
import { type DynamicTypeOperatorRegistry } from '../../../../../../../modules/operators/dynamic-type-operator-registry'
import { useOperator } from '../../../../../../../modules/operators/hooks/use-operator'
import { OperatorGridItem } from './operator-grid-item'

interface OperatorsSidebarProps {
  operatorRegistryServiceId: string
  gridContainerClassName: string
  groupName: string
  translatedGroupName: string
}

export const OperatorsSidebar = ({
  operatorRegistryServiceId,
  gridContainerClassName,
  groupName,
  translatedGroupName
}: OperatorsSidebarProps): React.JSX.Element => {
  const { t } = useTranslation()
  const operatorRegistry = useInjection<DynamicTypeOperatorRegistry>(operatorRegistryServiceId)
  const { getLocalizedName, getGroup, getIcon } = useOperator()

  const groupData = useMemo(() => {
    const operators = operatorRegistry.getDynamicTypes()
    const subGroups = new Map<string | undefined, Array<{
      id: string
      icon: any
      localizedName: string
    }>>()

    // Filter and organize operators for this specific group
    operators.forEach(operator => {
      if (operator.getGroup() !== groupName) return

      const translatedSubGroup = getGroup(operator).subGroup

      if (!subGroups.has(translatedSubGroup)) {
        subGroups.set(translatedSubGroup, [])
      }

      subGroups.get(translatedSubGroup)?.push({
        id: operator.id,
        icon: getIcon(operator, operatorRegistry),
        localizedName: getLocalizedName(operator)
      })
    })

    return subGroups
  }, [operatorRegistry, groupName, getLocalizedName, getGroup, getIcon])

  return (
    <>
      <SidebarTitle withBorder>
        {translatedGroupName}
      </SidebarTitle>

      <Box>
        {Array.from(groupData.entries()).map(([subGroupKey, operators]) => {
          const sortedOperators = operators.sort((a, b) => a.localizedName.localeCompare(b.localizedName))

          if (subGroupKey === undefined) {
            // No subgroup - render operators directly in grid
            return (
              <Box
                className={ gridContainerClassName }
                key={ `${translatedGroupName}-direct` }
                padding={ { x: 'extra-small', bottom: 'small' } }
              >
                {sortedOperators.map(operator => (
                  <OperatorGridItem
                    groupKey={ translatedGroupName }
                    key={ `${translatedGroupName}-${operator.id}` }
                    operator={ operator }
                  />
                ))}
              </Box>
            )
          } else {
            // Has subgroup - use collapsible panel
            return (
              <Panel
                border={ false }
                collapsed={ false }
                collapsible
                contentPadding="extra-small"
                key={ `${translatedGroupName}-${subGroupKey}` }
                theme="card-with-highlight"
                title={ t(subGroupKey) }
              >
                <Box className={ gridContainerClassName }>
                  {sortedOperators.map(operator => (
                    <OperatorGridItem
                      groupKey={ translatedGroupName }
                      key={ `${translatedGroupName}-${subGroupKey}-${operator.id}` }
                      operator={ operator }
                      subGroupKey={ subGroupKey }
                    />
                  ))}
                </Box>
              </Panel>
            )
          }
        })}
      </Box>
    </>
  )
}
