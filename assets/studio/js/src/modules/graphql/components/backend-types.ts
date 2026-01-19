/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

// Backend configuration types
export interface BackendWorkspace {
  cpath: string
  create: boolean
  read: boolean
  update: boolean
  delete: boolean
}

export interface BackendPermission {
  id?: number
  name?: string
  role?: string
  user?: string
  read?: boolean
  update?: boolean
  delete?: boolean
}

export interface BackendSchemaEntity {
  id: string
  entity: string
  name?: string
  [key: string]: any
}

export interface BackendSpecialEntity {
  name: string
  readAllowed: boolean
  createAllowed: boolean
  updateAllowed: boolean
  deleteAllowed: boolean
  readPossible?: boolean
  createPossible?: boolean
  updatePossible?: boolean
  deletePossible?: boolean
}

export interface BackendConfiguration {
  general?: {
    active: boolean
    description: string
    group: string
    [key: string]: any
  }
  security?: {
    method: string
    apikey: string | string[]
    skipPermissionCheck: boolean
    disableIntrospection: boolean
    [key: string]: any
  }
  workspaces?: {
    document?: BackendWorkspace[]
    asset?: BackendWorkspace[]
    object?: BackendWorkspace[]
  }
  permissions?: {
    role?: BackendPermission[]
    user?: BackendPermission[]
  }
  schema?: {
    queryEntities?: Record<string, BackendSchemaEntity> | BackendSchemaEntity[]
    mutationEntities?: Record<string, BackendSchemaEntity> | BackendSchemaEntity[]
    specialEntities?: BackendSpecialEntity[]
    [key: string]: any
  }
  [key: string]: any
}
