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
import { injectable } from '@pimcore/studio-ui-bundle/app'
import type { ElementIcon } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { DynamicTypeDataHubAdapterAbstract, type AdapterFormProps } from '../dynamic-type-data-hub-adapter-abstract'
import { GraphQLAdapterForm } from '../../../graphql/components/graphql-adapter-form'

@injectable()
export class DynamicTypeDataHubAdapterGraphQL extends DynamicTypeDataHubAdapterAbstract {
  readonly id = 'graphql'

  getIcon (): ElementIcon {
    return { type: 'name', value: 'graphql' }
  }

  getFormComponent (props: AdapterFormProps): React.JSX.Element {
    return <GraphQLAdapterForm { ...props } />
  }
}
