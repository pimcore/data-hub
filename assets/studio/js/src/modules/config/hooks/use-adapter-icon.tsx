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
import { Icon } from '@pimcore/studio-ui-bundle/components'
import { container } from '@pimcore/studio-ui-bundle/app'
import { type DynamicTypeDataHubAdapterRegistry } from '../dynamic-types/dynamic-type-data-hub-adapter-registry'
import { bundleServiceIds } from '../../../config/service-ids'
import { isUndefined } from 'lodash'

export const useAdapterIcon = (adapterType: string | undefined): React.JSX.Element => {
  const adapterRegistry = container.get<DynamicTypeDataHubAdapterRegistry>(
    bundleServiceIds['DataHub/DynamicTypes/Adapter/Registry']
  )

  if (isUndefined(adapterType)) {
    return <Icon value="database" />
  }

  try {
    const adapter = adapterRegistry.getDynamicType(adapterType, false)
    return adapter?.getIcon() ?? <Icon value="database" />
  } catch (error) {
    console.error('Error getting adapter icon:', error)
    return <Icon value="database" />
  }
}
