/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */


export type Doc = Record<string, any>

const keyBy = (list: Doc[], key: (item: Doc) => string, value: (item: Doc) => Doc): Doc =>
  list.reduce<Doc>((keyed, item) => ({ ...keyed, [key(item)]: value(item) }), {})

/**
 * The live document as it is stored: the detail endpoint lists the schema entities and the
 * generic types, while a change set addresses them by class name and by type.
 */
export function toStoredShape (live: Doc): Doc {
  const doc: Doc = structuredClone(live)
  const schema: Doc = doc.schema ?? {}
  for (const key of ['queryEntities', 'mutationEntities']) {
    if (Array.isArray(schema[key])) {
      schema[key] = keyBy(schema[key] as Doc[], (entity) => entity.id ?? entity.name, (entity) => entity)
    }
  }
  if (Array.isArray(schema.specialEntities)) {
    schema.specialEntities = keyBy(schema.specialEntities as Doc[], (setting) => setting.name, (setting) => ({
      ...setting,
      read: setting.readAllowed ?? false,
      create: setting.createAllowed ?? false,
      update: setting.updateAllowed ?? false,
      delete: setting.deleteAllowed ?? false
    }))
  }
  doc.schema = schema

  return doc
}

/** the generic types back as the list the editor reads; the schema entities it reads keyed */
export function toEditorShape (doc: Doc): Doc {
  const special = doc.schema?.specialEntities
  if (special !== undefined && special !== null && !Array.isArray(special)) {
    doc.schema.specialEntities = Object.entries(special as Record<string, Doc>).map(([name, setting]) => ({
      ...setting,
      name,
      readAllowed: setting.read ?? setting.readAllowed ?? false,
      createAllowed: setting.create ?? setting.createAllowed ?? false,
      updateAllowed: setting.update ?? setting.updateAllowed ?? false,
      deleteAllowed: setting.delete ?? setting.deleteAllowed ?? false
    }))
  }

  return doc
}

/** Un-flattens `a.b.c` leaves back into a nested object. */
export function assign (target: Record<string, unknown>, address: string, value: unknown): void {
  const segments = address.split('.')
  let node = target
  segments.forEach((segment, index) => {
    if (index === segments.length - 1) {
      node[segment] = value
      return
    }
    if (typeof node[segment] !== 'object' || node[segment] === null) {
      node[segment] = {}
    }
    node = node[segment] as Record<string, unknown>
  })
}

/** Removes an `a.b.c` leaf, and every object it leaves empty under its section. */
export function unassign (target: Record<string, unknown>, address: string): void {
  const segments = address.split('.')
  const parents: Array<Record<string, unknown>> = []
  let node = target
  for (const segment of segments.slice(0, -1)) {
    if (typeof node[segment] !== 'object' || node[segment] === null) return
    parents.push(node)
    node = node[segment] as Record<string, unknown>
  }
  Reflect.deleteProperty(node, segments[segments.length - 1])

  for (let depth = parents.length - 1; depth > 0 && Object.keys(node).length === 0; depth--) {
    Reflect.deleteProperty(parents[depth], segments[depth])
    node = parents[depth]
  }
}
