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
import { DynamicTypeDataHubAdapterAbstract, type DataHubAdapterDetailViewProps } from '../dynamic-type-data-hub-adapter-abstract'
import { GraphQLDetailView } from '../../../graphql/components/graphql-detail-view'

@injectable()
export class DynamicTypeDataHubAdapterGraphQL extends DynamicTypeDataHubAdapterAbstract {
  readonly id = 'graphql'

  getIcon (): ElementIcon {
    return { type: 'name', value: 'graphql', colorToken: 'colorCodingViolet4' }
  }

  renderDetailView (props: DataHubAdapterDetailViewProps): React.JSX.Element {
    return <GraphQLDetailView { ...props } />
  }
}
