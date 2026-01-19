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

// Workspace transformers
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

// Permission transformers
export function transformPermissionToBackend (permission: Permission, type: 'role' | 'user'): BackendPermission {
  // Create a clean object with only the fields the backend expects
  const backendPermission: BackendPermission = {
    name: permission.name, // Classic UI needs this for display
    read: permission.read ?? false,
    update: permission.update ?? false,
    delete: permission.delete ?? false
  }

  // Set either role or user field based on type
  if (type === 'role') {
    backendPermission.role = permission.name
  } else {
    backendPermission.user = permission.name
  }

  return backendPermission
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
  const result: BackendConfiguration['permissions'] = {}

  if (!isNil(permissions) && (permissions.roles?.length ?? 0) > 0) {
    result.role = permissions.roles.map(perm => transformPermissionToBackend(perm, 'role'))
  }

  if (!isNil(permissions) && (permissions.users?.length ?? 0) > 0) {
    result.user = permissions.users.map(perm => transformPermissionToBackend(perm, 'user'))
  }

  return result
}

export function transformPermissionsFromBackend (backendPermissions: BackendConfiguration['permissions']): GraphQLFormValues['permissions'] {
  return {
    roles: (backendPermissions?.role ?? []).map(perm => transformPermissionFromBackend(perm, 'role')),
    users: (backendPermissions?.user ?? []).map(perm => transformPermissionFromBackend(perm, 'user'))
  }
}

// Schema transformers

/**
 * Checks if the entity field is a meaningful value that differs from the name field.
 * This is needed because the UI adds an 'entity' field for display purposes,
 * but the backend may already have a 'name' field with the same value.
 * We only want to send 'entity' to the backend if it provides unique information.
 *
 * @param entityField - The entity field value from the UI
 * @param name - The name field value (may be undefined)
 * @returns true if entity field should be included in backend payload
 */
function shouldIncludeEntityField (entityField: unknown, name: unknown): boolean {
  return !isNil(entityField) && isString(entityField) && (isNil(name) || entityField !== name)
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

/**
 * Transforms schema entities from UI format to backend format.
 * Handles both array and object-based entity structures.
 * Filters out duplicate 'entity' fields that match the 'name' field to avoid redundant data.
 *
 * @param entities - Schema entities in UI format (can be array or object)
 * @returns Array of schema entities in backend format
 */
export function transformSchemaEntitiesToBackend (entities: Record<string, unknown> | unknown[]): BackendSchemaEntity[] {
  if (Array.isArray(entities)) {
    return entities.map((entity: any) => {
      const { entity: entityField, ...rest } = entity

      if (shouldIncludeEntityField(entityField, rest.name)) {
        return { ...rest, entity: entityField }
      }
      return rest
    })
  }

  return Object.entries(entities).map(([id, entity]: [string, any]) => {
    const { entity: entityField, ...rest } = entity
    return {
      id,
      ...rest,
      ...(shouldIncludeEntityField(entityField, rest.name) ? { entity: entityField } : {})
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
      entity: entity.entity ?? entity.name ?? entity.id
    }))
  }

  return Object.entries(backendEntities as Record<string, unknown>).map(([id, entity]: [string, any]) => ({
    id,
    entity: entity.entity ?? entity.name ?? id,
    ...entity
  }))
}

// Security transformers
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

// Full form transformers
export function transformFormToBackend (
  formValues: GraphQLFormValues,
  existingConfig: BackendConfiguration
): BackendConfiguration {
  return {
    ...existingConfig,
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
      queryEntities: formValues.schema?.query ?? [],
      mutationEntities: formValues.schema?.mutation ?? [],
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
