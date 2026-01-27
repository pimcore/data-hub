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
import React from 'react'
import { isNil } from 'lodash'
import { Text } from '@pimcore/studio-ui-bundle/components'
import { isNonEmptyString } from '@pimcore/studio-ui-bundle/utils'
import type { ElementIcon } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { DynamicTypeOperatorAbstract, type OperatorConfigModalProps, type ColumnConfig } from '../../dynamic-type-operator-abstract'
import { ThumbnailConfigModal } from './thumbnail-config-modal'

export interface ThumbnailAttributes {
  label: string
  thumbnailConfig?: string
}

@injectable()
export class DynamicTypeOperatorThumbnail extends DynamicTypeOperatorAbstract<ThumbnailAttributes> {
  readonly id = 'Thumbnail'

  getIcon (): ElementIcon {
    return { type: 'name', value: 'image-thumbnail' }
  }

  getLabel (config: ColumnConfig<ThumbnailAttributes>, localizedName: string): React.ReactNode {
    const label = config.attributes.label
    const thumbnailConfig = config.attributes.thumbnailConfig
    const displayLabel = isNonEmptyString(label) ? label : localizedName

    if (!isNil(thumbnailConfig) && thumbnailConfig !== '') {
      return (
        <>
          {displayLabel} <Text type="secondary">({thumbnailConfig})</Text>
        </>
      )
    }

    return displayLabel
  }

  getGroup (): string {
    return 'transformer'
  }

  getSubGroup (): string | undefined {
    return 'other'
  }

  getConfigModal (props: OperatorConfigModalProps<ThumbnailAttributes>): React.JSX.Element {
    return <ThumbnailConfigModal { ...props } />
  }

  getMaxChildCount (): number {
    return 1
  }
}
