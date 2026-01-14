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
import { Icon } from '@pimcore/studio-ui-bundle/components'
import { DynamicTypeDataHubAdapterAbstract } from '../dynamic-type-data-hub-adapter-abstract'

@injectable()
export class DynamicTypeDataHubAdapterGraphQL extends DynamicTypeDataHubAdapterAbstract {
  readonly id = 'graphql'
  readonly name = 'GraphQL'

  getIcon (): React.JSX.Element {
    return <Icon value="data-object-variant" />
  }
}
