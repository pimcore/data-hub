/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import type React from 'react'
import { injectable } from '@pimcore/studio-ui-bundle/app'
import type { ElementIcon } from '@pimcore/studio-ui-bundle/modules/widget-manager'

export interface DataHubAdapterDetailViewProps {
  configName: string
  configId: string
  isActive: boolean
  hasStudioColumnConfig: boolean
  onChange: (isDirty: boolean) => void
  onDelete: () => void
}

@injectable()
export abstract class DynamicTypeDataHubAdapterAbstract {
  abstract readonly id: string
  abstract getIcon (): ElementIcon
  abstract renderDetailView (props: DataHubAdapterDetailViewProps): React.JSX.Element

  getNameTranslationKey (): string {
    return `data-hub.adapter.${this.id}`
  }
}
