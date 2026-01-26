/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { injectable } from '@pimcore/studio-ui-bundle/app'
import { DynamicTypeRegistryAbstract } from '@pimcore/studio-ui-bundle/modules/element'
import { type DynamicTypeOperatorAbstract } from './dynamic-type-operator-abstract'
import type { ElementIcon } from '@pimcore/studio-ui-bundle/modules/widget-manager'

export interface GroupConfig {
  icon: ElementIcon
  priority: number
}

@injectable()
export class DynamicTypeOperatorRegistry extends DynamicTypeRegistryAbstract<DynamicTypeOperatorAbstract> {
  private readonly groupConfigs = new Map<string, GroupConfig>()

  registerGroupConfig (group: string, config: GroupConfig): void {
    this.groupConfigs.set(group, config)
  }

  getGroupConfig (group: string): GroupConfig | undefined {
    return this.groupConfigs.get(group)
  }
}
