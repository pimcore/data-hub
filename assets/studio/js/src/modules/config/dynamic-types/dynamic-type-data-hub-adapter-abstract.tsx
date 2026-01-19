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
import { type BundleDataHubConfigurationDetail } from '../config-api-slice-enhanced'

export interface AdapterFormProps {
  config: BundleDataHubConfigurationDetail
  configName: string
  configId: string
  onChange: (isDirty: boolean) => void
  isActive: boolean
}

@injectable()
export abstract class DynamicTypeDataHubAdapterAbstract {
  abstract readonly id: string
  abstract getIcon (): React.JSX.Element
  abstract getFormComponent (props: AdapterFormProps): React.JSX.Element

  getNameTranslationKey (): string {
    return `data-hub.adapter.${this.id}`
  }
}
