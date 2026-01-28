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
import { isNonEmptyString } from '@pimcore/studio-ui-bundle/utils'
import type { ElementIcon } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { DynamicTypeOperatorAbstract, type OperatorConfigModalProps, type ColumnConfig } from '../../dynamic-type-operator-abstract'
import { LocaleCollectorConfigModal } from './locale-collector-config-modal'

export interface LocaleCollectorAttributes {
  label: string
}

@injectable()
export class DynamicTypeOperatorLocaleCollector extends DynamicTypeOperatorAbstract<LocaleCollectorAttributes> {
  readonly id = 'LocaleCollector'

  getIcon (): ElementIcon {
    return { type: 'name', value: 'locale-collector' }
  }

  getLabel (config: ColumnConfig<LocaleCollectorAttributes>, localizedName: string): React.ReactNode {
    const label = config.attributes.label
    return isNonEmptyString(label) ? label : localizedName
  }

  getGroup (): string {
    return 'other'
  }

  getConfigModal (props: OperatorConfigModalProps<LocaleCollectorAttributes>): React.JSX.Element {
    return <LocaleCollectorConfigModal { ...props } />
  }

  getMaxChildCount (): number {
    return 1
  }

  allowedChildTypes (): string[] {
    return [
      'booleanSelect',
      'checkbox',
      'country',
      'countrymultiselect',
      'date',
      'datetime',
      'email',
      'externalImage',
      'geopoint',
      'firstname',
      'gender',
      'input',
      'image',
      'language',
      'lastname',
      'newsletterActive',
      'manyToOneRelation',
      'multiselect',
      'newsletterConfirmed',
      'numeric',
      'select',
      'slider',
      'textarea',
      'time',
      'wysiwyg'
    ]
  }
}
