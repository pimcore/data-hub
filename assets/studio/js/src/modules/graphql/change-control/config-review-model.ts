/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import type { ChangeSetReviewData, CompareTarget, ReviewSlotData } from '@pimcore/change-control-bundle/sdk'
import { type BackendConfiguration } from '../components/backend-types'
import { type FormAnnotations, type FormItemAnnotationStatus } from './studio-form-annotations'
import { assign, type Doc, toEditorShape, toStoredShape, unassign } from './document-shape'

const isEqual = (a: unknown, b: unknown): boolean => JSON.stringify(a ?? null) === JSON.stringify(b ?? null)

/**
 * The side a change is read against. While a change set is open that is the live document,
 * so a reviewer sees what approving would do now. Once it is resolved the live document has
 * moved on - after a merge it IS the proposal - and only the base it was recorded against
 * still says what changed.
 */
const comparedSide = (slot: ReviewSlotData, compareAgainst: CompareTarget): Record<string, unknown> =>
  (compareAgainst === 'base' ? slot.base : slot.current) ?? {}

export interface ConfigChange {
  /** document path — what the merge accepts as an exclude path */
  readonly address: string
  /** the same field as the editor's form binds it, when it binds it at all */
  readonly formPath?: string
  /** the field's own name, which is what a reader recognises */
  readonly label: string
  readonly section: string
  readonly status: FormItemAnnotationStatus
  readonly current: unknown
  readonly proposed: unknown
}

/** the general.* keys the editor's form lifts to its root; see transformBackendToForm */
const FLATTENED = new Set(['active', 'description', 'group'])

/** identity and bookkeeping: the subject's, never a change a reviewer weighs */
const NOT_A_CHANGE = new Set(['general.name', 'general.type', 'general.path'])

/**
 * A document path as the editor's form binds it, or undefined when the form has no field for
 * it — bookkeeping under `general`, most of all, which a reviewer must not be offered.
 */
export function toFormPath (address: string): string | undefined {
  const segments = address.split('.')
  if (segments[0] === 'general') {
    return segments.length === 2 && FLATTENED.has(segments[1]) ? segments[1] : undefined
  }
  // the form lists workspaces under the plural; see transformBackendToForm
  if (segments[0] === 'workspaces' && segments.length === 2) return `workspaces.${segments[1]}s`
  // the form binds each schema grid as one list, so a change anywhere in it marks the grid
  if (segments[0] === 'schema') return SCHEMA_GRIDS[segments[1]]
  return address
}

/** the stored schema keys, and the grid the editor shows each as */
const SCHEMA_GRIDS: Record<string, string> = {
  queryEntities: 'schema.query',
  mutationEntities: 'schema.mutation',
  specialEntities: 'schema.genericTypes'
}

/**
 * The configuration as the change set proposes it: the live document with the proposed leaves
 * laid over it and the leaves it drops taken out.
 *
 * The live document has to come from the bundle's own endpoint. A review payload carries only
 * the CHANGED subset of the state, which is the right thing for a diff and far too little for
 * an editor — mounted on it alone, every untouched select renders empty. It is also the only
 * source for the sections a proposal may never carry, which still have to render.
 */
export function proposedConfiguration (
  payload: ChangeSetReviewData | undefined,
  live: BackendConfiguration | undefined
): BackendConfiguration {
  const config = toStoredShape((live ?? {}) as Doc)
  if (payload == null) return toEditorShape(config) as BackendConfiguration

  // a proposal replaces whole sections, so a live leaf it does not carry is one approving removes
  for (const slot of Object.values(payload.slots ?? {})) {
    const proposed = slot.proposed ?? {}
    for (const address of Object.keys(slot.current ?? {})) {
      if (!(address in proposed)) unassign(config, address)
    }
    for (const [address, value] of Object.entries(proposed)) assign(config, address, value)
  }

  return toEditorShape(config) as BackendConfiguration
}

/** what the editor calls each slot, as translation keys, in the order it shows them */
export const SECTION_LABELS: Record<string, string> = {
  general: 'data-hub.tabs.general',
  schema: 'data-hub.tabs.schema-definition',
  security: 'data-hub.tabs.security-definition'
}

/**
 * Where a section lives in the editor. This is what lets the rail navigate rather than only
 * list.
 */
export const SECTION_TARGET: Record<string, { tab: string }> = {
  general: { tab: 'general' },
  schema: { tab: 'schema' },
  security: { tab: 'security' }
}

export interface ChangeGroup {
  readonly section: string
  readonly label: string
  readonly changes: ConfigChange[]
}

/** Changes grouped the way the editor is laid out, so the rail reads as the form does. */
export function groupChanges (changes: ConfigChange[]): ChangeGroup[] {
  const bySection = new Map<string, ConfigChange[]>()
  for (const change of changes) {
    const list = bySection.get(change.section) ?? []
    list.push(change)
    bySection.set(change.section, list)
  }

  const ordered = [...Object.keys(SECTION_LABELS), ...bySection.keys()]
  const seen = new Set<string>()
  const groups: ChangeGroup[] = []
  for (const section of ordered) {
    if (seen.has(section)) continue
    seen.add(section)
    const list = bySection.get(section)
    if (list === undefined || list.length === 0) continue
    groups.push({ section, label: SECTION_LABELS[section] ?? section, changes: list })
  }

  return groups
}

/**
 * True when the change set CREATES the configuration rather than changing one. Nothing has a
 * previous value, so enumerating every field says only "all of it" at great length.
 *
 * Change Control's own answer wins: the payload carries only the changed sections, so an edit
 * that touches only sections the live document lacks has nothing to compare against either.
 */
export function isNewConfiguration (
  payload: ChangeSetReviewData | undefined,
  compareAgainst: CompareTarget = 'current'
): boolean {
  const subject = payload?.subjects?.find(
    (candidate) => candidate.subjectType === payload.subjectType && candidate.subjectRef === payload.subjectRef
  )
  if (subject?.isNew !== undefined) return subject.isNew

  const slots = Object.values(payload?.slots ?? {})
  if (slots.length === 0) return false

  return slots.every((slot) => Object.keys(comparedSide(slot, compareAgainst)).length === 0)
}

/** Every leaf the change set actually changes, in the order the editor shows the sections. */
export function configChanges (
  payload: ChangeSetReviewData | undefined,
  compareAgainst: CompareTarget = 'current'
): ConfigChange[] {
  if (payload == null) return []

  const changes: ConfigChange[] = []
  // the entities each side holds, so one that exists on one side only reads as a whole
  const onBothSides = new Set<string>()
  for (const [slotKey, slot] of Object.entries(payload.slots ?? {})) {
    const current = comparedSide(slot, compareAgainst)
    const proposed = slot.proposed ?? {}
    changes.push(...slotChanges(slotKey, current, proposed))
    for (const entity of entitiesOnBothSides(current, proposed)) onBothSides.add(entity)
  }

  return collapseWholeEntities(changes, onBothSides)
}

function slotChanges (
  section: string,
  current: Record<string, unknown>,
  proposed: Record<string, unknown>
): ConfigChange[] {
  const changes: ConfigChange[] = []
  for (const address of new Set([...Object.keys(proposed), ...Object.keys(current)])) {
    if (NOT_A_CHANGE.has(address)) continue
    const inProposed = address in proposed
    const inCurrent = address in current
    if (inProposed && inCurrent && isEqual(current[address], proposed[address])) continue
    changes.push({
      address,
      formPath: toFormPath(address),
      label: address.split('.').pop() ?? address,
      section,
      status: changeStatus(inCurrent, inProposed),
      current: current[address],
      proposed: proposed[address]
    })
  }
  return changes
}

function changeStatus (inCurrent: boolean, inProposed: boolean): FormItemAnnotationStatus {
  if (!inCurrent) return 'added'
  return inProposed ? 'changed' : 'removed'
}

function entitiesOnBothSides (current: Record<string, unknown>, proposed: Record<string, unknown>): string[] {
  const proposedEntities = new Set(Object.keys(proposed).map(entityOf))
  return Object.keys(current)
    .map(entityOf)
    .filter((entity): entity is string => entity !== undefined && proposedEntities.has(entity))
}

/** `schema.queryEntities.Car` for any leaf inside that entity */
function entityOf (address: string): string | undefined {
  const segments = address.split('.')
  return segments[0] === 'schema' && segments.length > 3 ? segments.slice(0, 3).join('.') : undefined
}

/**
 * A schema entity the proposal adds or drops arrives as one leaf per key — id, name, columns.
 * The reviewer weighs the entity, so every leaf of one that exists on one side only is one row.
 */
function collapseWholeEntities (changes: ConfigChange[], onBothSides: Set<string>): ConfigChange[] {
  const collapsed: ConfigChange[] = []
  const seen = new Set<string>()

  for (const change of changes) {
    const entity = entityOf(change.address)
    if (entity === undefined || onBothSides.has(entity)) {
      collapsed.push(change)
      continue
    }
    if (seen.has(entity)) continue
    seen.add(entity)
    collapsed.push({ ...change, address: entity, label: entity.split('.').pop() ?? entity, current: undefined, proposed: undefined })
  }

  return collapsed
}

/** the review's marks on the fields the form binds: the status alone, the field says the rest */
export function annotationsFor (changes: ConfigChange[]): FormAnnotations {
  const annotations: FormAnnotations = {}
  for (const change of changes) {
    if (change.formPath === undefined) continue
    annotations[change.formPath] = { status: change.status }
  }
  return annotations
}
