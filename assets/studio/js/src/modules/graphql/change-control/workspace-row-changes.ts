/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { createContext, useContext } from 'react'
import { type BackendWorkspace } from '../components/backend-types'
import { type Workspace, type WorkspaceType } from '../components/tabs/security-definition-tab/types'
import { type FormItemAnnotationStatus } from './studio-form-annotations'
import { type ConfigChange } from './config-review-model'

export type WorkspaceRowStatus = 'added' | 'changed' | 'removed'

export interface WorkspaceRowChanges {
  /** the list's own status, for its header */
  readonly status: FormItemAnnotationStatus
  /** the status of each proposed row that differs from the compared side, by its path */
  readonly byPath: Record<string, WorkspaceRowStatus>
  /** rows the proposal drops; the proposed form no longer holds them */
  readonly removed: Workspace[]
}

/** the stored list each grid shows */
const STORED: Record<WorkspaceType, string> = {
  documents: 'workspaces.document',
  assets: 'workspaces.asset',
  objects: 'workspaces.object'
}

const FLAGS = ['create', 'read', 'update', 'delete'] as const

const rows = (value: unknown): BackendWorkspace[] => Array.isArray(value) ? value as BackendWorkspace[] : []

const differs = (before: BackendWorkspace, after: BackendWorkspace): boolean =>
  FLAGS.some((flag) => (before[flag] ?? false) !== (after[flag] ?? false))

const byCpath = (list: BackendWorkspace[]): Map<string, BackendWorkspace[]> => {
  const grouped = new Map<string, BackendWorkspace[]>()
  for (const row of list) grouped.set(row.cpath, [...(grouped.get(row.cpath) ?? []), row])
  return grouped
}

// a path listed twice is compared occurrence by occurrence, so no added or dropped duplicate goes unmarked
function listChanges (change: ConfigChange): WorkspaceRowChanges {
  const before = byCpath(rows(change.current))
  const after = byCpath(rows(change.proposed))

  const byPath: Record<string, WorkspaceRowStatus> = {}
  for (const [cpath, proposed] of after) {
    const previous = before.get(cpath)
    if (previous === undefined) byPath[cpath] = 'added'
    else if (previous.length !== proposed.length || proposed.some((row, index) => differs(previous[index], row))) byPath[cpath] = 'changed'
  }

  // the occurrences beyond the proposed count are the dropped ones
  const dropped: BackendWorkspace[] = []
  before.forEach((previous, cpath) => { dropped.push(...previous.slice(after.get(cpath)?.length ?? 0)) })
  const removed = dropped
    .map((row) => ({
      path: row.cpath,
      create: row.create ?? false,
      read: row.read ?? false,
      update: row.update ?? false,
      delete: row.delete ?? false
    }))

  return { status: change.status, byPath, removed }
}

/**
 * Each workspace list changes as one leaf (the whole list), so the form alone can only mark the
 * list. This splits each changed list into rows, matched by path.
 */
export function workspaceRowChanges (changes: ConfigChange[]): Partial<Record<WorkspaceType, WorkspaceRowChanges>> {
  const result: Partial<Record<WorkspaceType, WorkspaceRowChanges>> = {}
  for (const type of Object.keys(STORED) as WorkspaceType[]) {
    const change = changes.find((candidate) => candidate.address === STORED[type])
    if (change !== undefined) result[type] = listChanges(change)
  }
  return result
}

export const WorkspaceRowChangesContext = createContext<Partial<Record<WorkspaceType, WorkspaceRowChanges>>>({})

/** undefined outside a review or for a list the review leaves as it is, which leaves the grid as it is */
export const useWorkspaceRowChanges = (type: WorkspaceType): WorkspaceRowChanges | undefined =>
  useContext(WorkspaceRowChangesContext)[type]

/** a picked element is a reference until the form is saved; the review compares by path */
export const workspacePathOf = (path: unknown): string =>
  typeof path === 'string' ? path : ((path as { fullPath?: string } | null)?.fullPath ?? '')
