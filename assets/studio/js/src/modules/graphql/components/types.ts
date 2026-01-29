/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { type Permission } from './tabs/permissions-tab/types'
import { type Workspace } from './tabs/security-definition-tab/types'
import { type QueryEntity, type MutationEntity, type GenericType } from './tabs/schema-definition-tab/types'

export interface GraphQLFormValues {
  active: boolean
  name: string
  description: string
  group: string
  security: {
    method: string
    apikey: string
    skipPermissionCheck: boolean
    disableIntrospection: boolean
  }
  workspaces: {
    documents: Workspace[]
    assets: Workspace[]
    objects: Workspace[]
  }
  permissions: {
    roles: Permission[]
    users: Permission[]
  }
  schema: {
    query: QueryEntity[]
    mutation: MutationEntity[]
    genericTypes: GenericType[]
  }
}

export type { Permission, Workspace, QueryEntity, MutationEntity, GenericType }
