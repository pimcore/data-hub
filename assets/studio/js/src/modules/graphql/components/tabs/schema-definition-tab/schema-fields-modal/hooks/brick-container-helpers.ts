/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

export interface ObjectBricksFieldInfo {
  name: string
  title: string | null
  allowedTypes: string[]
}

/**
 * Brick keys which are allowed in more than one objectbricks field of the class.
 * Their columns need the containing field in the attribute to be told apart.
 */
export const getAmbiguousBrickKeys = (fields: ObjectBricksFieldInfo[]): Set<string> => {
  const usage = new Map<string, Set<string>>()

  fields.forEach(field => {
    field.allowedTypes.forEach(brickKey => {
      usage.set(brickKey, (usage.get(brickKey) ?? new Set<string>()).add(field.name))
    })
  })

  return new Set(
    Array.from(usage.entries())
      .filter(([, fieldNames]) => fieldNames.size > 1)
      .map(([brickKey]) => brickKey)
  )
}

/**
 * `Brick~field` is the attribute format used so far and resolves to the first objectbricks field allowing
 * the brick. `Brick~field~containerField` selects the field explicitly; it is only needed (and only used)
 * for bricks which are allowed in several fields, so that existing configurations stay valid.
 * A field of the brick's localizedfields is addressed by a `?{"containerKey":…,"fieldname":…,"brickfield":…}`
 * descriptor in place of the brick key, as the classic UI writes it (see DataObjectFieldHelper::getFieldDefinitionFromKey()).
 */
export const buildBrickAttribute = (
  brickKey: string,
  brickFieldName: string,
  containerName: string,
  ambiguousBrickKeys: Set<string>,
  localized: boolean = false
): string => {
  const brickType = localized
    ? `?${JSON.stringify({ containerKey: brickKey, fieldname: containerName, brickfield: brickFieldName })}`
    : brickKey
  const attribute = `${brickType}~${brickFieldName}`

  return ambiguousBrickKeys.has(brickKey) ? `${attribute}~${containerName}` : attribute
}

export const buildBrickColumnTitle = (
  label: string,
  brickKey: string,
  brickFieldName: string,
  containerName: string,
  ambiguousBrickKeys: Set<string>
): string => {
  const path = ambiguousBrickKeys.has(brickKey)
    ? `${containerName}.${brickKey}.${brickFieldName}`
    : `${brickKey}.${brickFieldName}`

  return `${label} (${path})`
}

/**
 * The names of the fields in the localizedfields of a brick layout. Only layout elements are searched on the way,
 * so that a field in the localizedfields of a block (whose name may repeat a field of the brick) is not included.
 */
export const collectLocalizedFieldNames = (layout: { children?: object[] }): Set<string> => {
  const names = new Set<string>()

  const walk = (children: object[], inLocalizedFields: boolean): void => {
    children.forEach(child => {
      const node = child as Record<string, unknown>

      if (node.datatype === 'data') {
        if (inLocalizedFields && typeof node.name === 'string') names.add(node.name)
        if (node.fieldtype !== 'localizedfields') return
      }

      if (Array.isArray(node.children)) {
        walk(node.children as object[], inLocalizedFields || node.fieldtype === 'localizedfields')
      }
    })
  }

  walk(layout.children ?? [], false)

  return names
}

export type BrickColumn = (label: string) => { attribute: string, title: string }

/** The column of each field of a brick in one objectbricks field of the class, by field name. */
export const createBrickColumnFactory = (
  brickKey: string,
  containerName: string,
  brickLayout: { children?: object[] },
  ambiguousBrickKeys: Set<string>
): (brickFieldName: string) => BrickColumn => {
  const localizedFieldNames = collectLocalizedFieldNames(brickLayout)

  return brickFieldName => label => ({
    attribute: buildBrickAttribute(brickKey, brickFieldName, containerName, ambiguousBrickKeys, localizedFieldNames.has(brickFieldName)),
    title: buildBrickColumnTitle(label, brickKey, brickFieldName, containerName, ambiguousBrickKeys)
  })
}
