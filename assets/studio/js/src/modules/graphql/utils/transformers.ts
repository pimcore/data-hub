/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { type ElementReference } from '@pimcore/studio-ui-bundle/modules/element'
import { isString, isNil } from 'lodash'
import { type GraphQLFormValues, type Workspace, type Permission, type GenericType } from '../components/types'
import { type BackendConfiguration, type BackendWorkspace, type BackendPermission, type BackendSchemaEntity, type BackendSpecialEntity } from '../components/backend-types'

export function extractElementPath (pathValue: ElementReference | string | null | undefined): string {
  if (isString(pathValue)) {
    return pathValue
  }
  return pathValue?.fullPath ?? ''
}

export function transformWorkspaceToBackend (workspace: Workspace): BackendWorkspace {
  return {
    cpath: extractElementPath(workspace.path as ElementReference | string | null),
    create: workspace.create ?? false,
    read: workspace.read ?? false,
    update: workspace.update ?? false,
    delete: workspace.delete ?? false
  }
}

export function transformWorkspaceFromBackend (backendWorkspace: BackendWorkspace): Workspace {
  return {
    path: backendWorkspace.cpath ?? '',
    create: backendWorkspace.create ?? false,
    read: backendWorkspace.read ?? false,
    update: backendWorkspace.update ?? false,
    delete: backendWorkspace.delete ?? false
  }
}

export function transformWorkspacesToBackend (workspaces: GraphQLFormValues['workspaces'] | undefined): BackendConfiguration['workspaces'] {
  return {
    document: (workspaces?.documents ?? []).map(transformWorkspaceToBackend),
    asset: (workspaces?.assets ?? []).map(transformWorkspaceToBackend),
    object: (workspaces?.objects ?? []).map(transformWorkspaceToBackend)
  }
}

export function transformWorkspacesFromBackend (backendWorkspaces: BackendConfiguration['workspaces']): GraphQLFormValues['workspaces'] {
  return {
    documents: (backendWorkspaces?.document ?? []).map(transformWorkspaceFromBackend),
    assets: (backendWorkspaces?.asset ?? []).map(transformWorkspaceFromBackend),
    objects: (backendWorkspaces?.object ?? []).map(transformWorkspaceFromBackend)
  }
}

export function transformPermissionToBackend (permission: Permission, type: 'role' | 'user'): BackendPermission {
  return {
    name: permission.name,
    read: permission.read ?? false,
    update: permission.update ?? false,
    delete: permission.delete ?? false
  }
}

export function transformPermissionFromBackend (backendPermission: BackendPermission, type: 'role' | 'user'): Permission {
  return {
    name: backendPermission.name ?? (type === 'role' ? (backendPermission.role ?? '') : (backendPermission.user ?? '')),
    read: backendPermission.read ?? false,
    update: backendPermission.update ?? false,
    delete: backendPermission.delete ?? false
  }
}

export function transformPermissionsToBackend (permissions: GraphQLFormValues['permissions'] | undefined): BackendConfiguration['permissions'] {
  return {
    role: (permissions?.roles ?? []).map(perm => transformPermissionToBackend(perm, 'role')),
    user: (permissions?.users ?? []).map(perm => transformPermissionToBackend(perm, 'user'))
  }
}

export function transformPermissionsFromBackend (backendPermissions: BackendConfiguration['permissions']): GraphQLFormValues['permissions'] {
  return {
    roles: (backendPermissions?.role ?? []).map(perm => transformPermissionFromBackend(perm, 'role')),
    users: (backendPermissions?.user ?? []).map(perm => transformPermissionFromBackend(perm, 'user'))
  }
}

export function transformGenericTypeToBackend (genericType: GenericType): BackendSpecialEntity {
  return {
    name: genericType.name,
    readAllowed: genericType.read ?? false,
    createAllowed: genericType.create ?? false,
    updateAllowed: genericType.update ?? false,
    deleteAllowed: genericType.delete ?? false
  }
}

export function transformGenericTypeFromBackend (backendEntity: BackendSpecialEntity): GenericType {
  return {
    name: backendEntity.name,
    readPossible: backendEntity.readPossible ?? false,
    createPossible: backendEntity.createPossible ?? false,
    updatePossible: backendEntity.updatePossible ?? false,
    deletePossible: backendEntity.deletePossible ?? false,
    read: backendEntity.readAllowed ?? false,
    create: backendEntity.createAllowed ?? false,
    update: backendEntity.updateAllowed ?? false,
    delete: backendEntity.deleteAllowed ?? false
  }
}

// Maps UI 'entity' field to backend 'name' field
export function transformSchemaEntitiesToBackend (entities: Record<string, unknown> | unknown[]): BackendSchemaEntity[] {
  if (Array.isArray(entities)) {
    return entities.map((entity: any) => {
      const { entity: entityField, id, ...rest } = entity
      return {
        id,
        name: entityField ?? rest.name ?? id,
        ...rest
      }
    })
  }

  return Object.entries(entities).map(([id, entity]: [string, any]) => {
    const { entity: entityField, ...rest } = entity
    return {
      id,
      name: entityField ?? rest.name ?? id,
      ...rest
    }
  })
}

export function transformSchemaEntitiesFromBackend (backendEntities: Record<string, BackendSchemaEntity> | BackendSchemaEntity[] | undefined, defaultValue: any[] = []): any[] {
  if (isNil(backendEntities)) {
    return defaultValue
  }

  if (Array.isArray(backendEntities)) {
    return backendEntities.map((entity: any) => ({
      ...entity,
      entity: entity.name ?? entity.id
    }))
  }

  return Object.entries(backendEntities as Record<string, unknown>).map(([id, entity]: [string, any]) => ({
    id,
    ...entity,
    entity: entity.name ?? id
  }))
}


export function transformApiKeyToBackend (apikey: string | undefined | null): string[] {
  if (isNil(apikey)) {
    return []
  }

  if (typeof apikey === 'string') {
    return apikey.split('\n').filter((key: string) => key.trim() !== '')
  }

  return []
}

export function transformApiKeyFromBackend (apikey: string | string[] | undefined | null): string {
  if (isNil(apikey)) {
    return ''
  }

  if (Array.isArray(apikey)) {
    return apikey.join('\n')
  }

  return apikey
}

export function transformFormToBackend (
  formValues: GraphQLFormValues,
  existingConfig: BackendConfiguration
): BackendConfiguration {
  const { userPermissions, ...restConfig } = existingConfig
  return {
    ...restConfig,
    general: {
      ...(existingConfig.general ?? {}),
      active: formValues.active,
      description: formValues.description,
      group: formValues.group
    },
    security: {
      ...(existingConfig.security ?? {}),
      method: formValues.security?.method,
      apikey: transformApiKeyToBackend(formValues.security?.apikey),
      skipPermissionCheck: formValues.security?.skipPermissionCheck ?? false,
      disableIntrospection: formValues.security?.disableIntrospection ?? false
    },
    workspaces: transformWorkspacesToBackend(formValues.workspaces),
    permissions: transformPermissionsToBackend(formValues.permissions),
    schema: {
      ...(existingConfig.schema ?? {}),
      queryEntities: transformSchemaEntitiesToBackend(formValues.schema?.query ?? []),
      mutationEntities: transformSchemaEntitiesToBackend(formValues.schema?.mutation ?? []),
      specialEntities: (formValues.schema?.genericTypes ?? []).map(transformGenericTypeToBackend)
    }
  }
}

export function transformBackendToForm (
  backendConfig: BackendConfiguration,
  configName: string
): GraphQLFormValues {
  return {
    active: backendConfig.general?.active ?? true,
    name: configName,
    description: backendConfig.general?.description ?? '',
    group: backendConfig.general?.group ?? '',
    security: {
      method: backendConfig.security?.method ?? 'datahub_apikey',
      apikey: transformApiKeyFromBackend(backendConfig.security?.apikey),
      skipPermissionCheck: backendConfig.security?.skipPermissionCheck ?? false,
      disableIntrospection: backendConfig.security?.disableIntrospection ?? false
    },
    workspaces: transformWorkspacesFromBackend(backendConfig.workspaces),
    permissions: transformPermissionsFromBackend(backendConfig.permissions),
    schema: {
      query: transformSchemaEntitiesFromBackend(backendConfig.schema?.queryEntities, []),
      mutation: transformSchemaEntitiesFromBackend(backendConfig.schema?.mutationEntities, []),
      genericTypes: (backendConfig.schema?.specialEntities ?? []).map(transformGenericTypeFromBackend)
    }
  }
}
