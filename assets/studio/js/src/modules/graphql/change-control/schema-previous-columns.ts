/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { type ConfigChange } from './config-review-model'

const COLUMNS_ADDRESS = /^schema\.(queryEntities|mutationEntities)\.([^.]+)\.columnConfig\.columns$/
// an entity the change adds whole arrives as one change for the entity
const ENTITY_ADDRESS = /^schema\.(queryEntities|mutationEntities)\.([^.]+)$/

/** each changed entity's fields before the change, by grid and entity id, so its fields modal can mark them */
export function schemaPreviousColumns (changes: ConfigChange[]): Partial<Record<'query' | 'mutation', Record<string, unknown[]>>> {
  const previous: Partial<Record<'query' | 'mutation', Record<string, unknown[]>>> = {}
  for (const change of changes) {
    const match = COLUMNS_ADDRESS.exec(change.address) ?? (change.status === 'added' ? ENTITY_ADDRESS.exec(change.address) : null)
    if (match === null) continue
    const grid = match[1] === 'queryEntities' ? 'query' : 'mutation'
    previous[grid] = { ...previous[grid], [match[2]]: Array.isArray(change.current) ? change.current : [] }
  }
  return previous
}
