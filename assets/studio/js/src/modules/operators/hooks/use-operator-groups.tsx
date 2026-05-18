/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React from 'react'
import type { DynamicTypeOperatorRegistry } from '../dynamic-type-operator-registry'
import type { ElementIcon } from '@pimcore/studio-ui-bundle/modules/widget-manager'

interface UseOperatorGroupsReturn {
  getGroupIcon: (groupName: string) => ElementIcon
}

export function useOperatorGroups (registry: DynamicTypeOperatorRegistry): UseOperatorGroupsReturn {
  const getGroupIcon = React.useCallback(
    (groupName: string): ElementIcon => {
      const groupConfig = registry.getGroupConfig(groupName)
      return groupConfig?.icon ?? { type: 'name' as const, value: 'data-object' }
    },
    [registry]
  )

  return {
    getGroupIcon
  }
}
