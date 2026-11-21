/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

/**
 * What the editor calls a field, by its DOCUMENT path.
 *
 * The rail names fields the way the form labels them, so a reader recognises the row they are
 * about to be carried to. Anything absent here is humanised from its own key rather than
 * shown as a path — a missing entry costs legibility, never correctness.
 */
const LABEL_KEYS: Record<string, string> = {
  'general.active': 'data-hub.config.active',
  'general.description': 'data-hub.config.description',
  'general.group': 'data-hub.config.group',
  'workspaces.document': 'data-hub.workspaces.documents',
  'workspaces.asset': 'data-hub.workspaces.assets',
  'workspaces.object': 'data-hub.workspaces.objects'
}

/** what the editor calls each schema grid */
const GRID_KEYS: Record<string, string> = {
  queryEntities: 'data-hub.review.field.query-entity',
  mutationEntities: 'data-hub.review.field.mutation-entity',
  specialEntities: 'data-hub.review.field.generic-type'
}

/** `columnConfig` → `Column config` */
function humanise (key: string): string {
  const words = key
    .replace(/[_-]+/g, ' ')
    .replace(/([a-z\d])([A-Z])/g, '$1 $2')
    .trim()
    .toLowerCase()

  return words.charAt(0).toUpperCase() + words.slice(1)
}

/**
 * The label for a document path: the editor's own translation where there is one, otherwise
 * the key read as words.
 */
export function fieldLabel (address: string, t: (key: string, options?: Record<string, unknown>) => string): string {
  // a schema leaf is named by its grid and its row: "Query entity Car — Column config"
  const segments = address.split('.')
  const grid = segments[0] === 'schema' ? GRID_KEYS[segments[1]] : undefined
  if (grid !== undefined && segments.length >= 3) {
    const row = t(grid, { name: segments[2] })
    return segments.length > 3 ? `${row} — ${humanise(segments[segments.length - 1])}` : row
  }

  const key = LABEL_KEYS[address]
  if (key !== undefined) {
    const translated = t(key)
    // the host translator echoes the key when it has no string for it
    if (translated !== key && translated !== '') return translated
  }

  return humanise(address.split('.').pop() ?? address)
}
