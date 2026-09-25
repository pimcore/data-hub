/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { type BackendConfiguration } from '../components/backend-types'
import { SECTION_LABELS } from './config-review-model'
import { fieldLabel } from './field-labels'

type Translate = (key: string, options?: Record<string, unknown>) => string

const T = 'data-hub.review.outline'

export interface BriefSection {
  /** the editor section this is about; the same key the change rail navigates by */
  readonly key: string
  /** translation key */
  readonly label: string
  readonly value: string
  readonly note?: string
}

/** A configuration that does not exist yet, told section by section rather than field by field. */
export interface ConfigBrief {
  readonly name: string
  readonly active: boolean
  readonly description?: string
  readonly sections: BriefSection[]
}

const text = (value: unknown): string | undefined =>
  typeof value === 'string' && value !== '' ? value : undefined

const plural = (t: Translate, key: string, count: number): string =>
  t(`${T}.${key}${count === 1 ? '-one' : ''}`, { count })

/**
 * A configuration that does not exist yet, told once. Every leaf of it is "added", so marking
 * them one by one says only "all of it" at great length: each section says instead what it is
 * set to.
 */
export function configBrief (configuration: BackendConfiguration, t: Translate): ConfigBrief {
  const general = configuration.general
  const group = text(general?.group)
  const schema = (configuration.schema ?? {}) as Record<string, unknown>
  const entities = (key: string): string[] => {
    const value = schema[key]
    const list = Array.isArray(value) ? value : Object.values((value ?? {}) as Record<string, unknown>)
    return list
      .map((entity) => text((entity as Record<string, unknown>)?.name) ?? text((entity as Record<string, unknown>)?.id))
      .filter((name): name is string => name !== undefined)
  }
  const queries = entities('queryEntities')
  const mutations = entities('mutationEntities')
  const workspaces = (configuration.workspaces ?? {}) as Record<string, unknown[] | undefined>
  const rules = ['document', 'asset', 'object'].reduce((sum, kind) => sum + (workspaces[kind]?.length ?? 0), 0)

  const sections: Array<BriefSection | undefined> = [
    group === undefined
      ? undefined
      : { key: 'general', label: SECTION_LABELS.general, value: `${fieldLabel('general.group', t)} ${group}` },
    queries.length === 0
      ? undefined
      : {
          key: 'schema',
          label: SECTION_LABELS.schema,
          value: queries.length <= 2 ? queries.join(', ') : plural(t, 'queries', queries.length),
          note: mutations.length === 0 ? undefined : plural(t, 'mutations', mutations.length)
        },
    // no workspace means the endpoint can read nothing yet, which is worth saying out loud
    { key: 'security', label: SECTION_LABELS.security, value: rules === 0 ? t(`${T}.nothing`) : plural(t, 'rules', rules) }
  ]

  return {
    name: text(general?.name) ?? '',
    active: general?.active === true,
    description: text(general?.description),
    sections: sections.filter((section): section is BriefSection => section !== undefined)
  }
}
