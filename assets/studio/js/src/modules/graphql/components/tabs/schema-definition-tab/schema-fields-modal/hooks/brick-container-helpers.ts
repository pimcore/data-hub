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
 */
export const buildBrickAttribute = (
  brickKey: string,
  brickFieldName: string,
  containerName: string,
  ambiguousBrickKeys: Set<string>
): string => {
  const attribute = `${brickKey}~${brickFieldName}`

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
