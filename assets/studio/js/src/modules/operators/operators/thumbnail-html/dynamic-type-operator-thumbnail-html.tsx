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
import { ThumbnailHtmlConfigModal } from './thumbnail-html-config-modal'

export interface ThumbnailHtmlAttributes {
  label: string
  thumbnailHtmlConfig?: string
}

@injectable()
export class DynamicTypeOperatorThumbnailHtml extends DynamicTypeOperatorAbstract<ThumbnailHtmlAttributes> {
  readonly id = 'ThumbnailHtml'

  getIcon (): ElementIcon {
    return { type: 'name', value: 'thumbnails' }
  }

  getLabel (config: ColumnConfig<ThumbnailHtmlAttributes>, localizedName: string): React.ReactNode {
    const label = config.attributes.label
    const thumbnailHtmlConfig = config.attributes.thumbnailHtmlConfig
    const displayLabel = isNonEmptyString(label) ? label : localizedName

    if (!isNil(thumbnailHtmlConfig) && thumbnailHtmlConfig !== '') {
      return (
        <>
          {displayLabel} <Text type="secondary">({thumbnailHtmlConfig})</Text>
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

  getConfigModal (props: OperatorConfigModalProps<ThumbnailHtmlAttributes>): React.JSX.Element {
    return <ThumbnailHtmlConfigModal { ...props } />
  }

  getMaxChildCount (): number {
    return 1
  }
}
