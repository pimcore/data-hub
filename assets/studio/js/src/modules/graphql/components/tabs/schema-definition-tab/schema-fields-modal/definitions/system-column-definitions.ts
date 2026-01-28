/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { type IconProps } from '@pimcore/studio-ui-bundle/components'

export interface SystemColumnDefinition {
  key: string
  attribute: string
  translationKey: string
  iconProps: IconProps
}

export const SYSTEM_COLUMN_ICON: IconProps = { value: 'workflow', colorToken: 'colorCodingGreen1' }

export const systemColumnDefinitions: SystemColumnDefinition[] = [
  {
    key: 'system-id',
    attribute: 'id',
    translationKey: 'data-hub.schema.system.id',
    iconProps: SYSTEM_COLUMN_ICON
  },
  {
    key: 'system-fullpath',
    attribute: 'fullpath',
    translationKey: 'data-hub.schema.system.fullpath',
    iconProps: SYSTEM_COLUMN_ICON
  },
  {
    key: 'system-key',
    attribute: 'key',
    translationKey: 'data-hub.schema.system.key',
    iconProps: SYSTEM_COLUMN_ICON
  },
  {
    key: 'system-published',
    attribute: 'published',
    translationKey: 'data-hub.schema.system.published',
    iconProps: SYSTEM_COLUMN_ICON
  },
  {
    key: 'system-creationDate',
    attribute: 'creationDate',
    translationKey: 'data-hub.schema.system.creationDate',
    iconProps: SYSTEM_COLUMN_ICON
  },
  {
    key: 'system-modificationDate',
    attribute: 'modificationDate',
    translationKey: 'data-hub.schema.system.modificationDate',
    iconProps: SYSTEM_COLUMN_ICON
  },
  {
    key: 'system-filename',
    attribute: 'filename',
    translationKey: 'data-hub.schema.system.filename',
    iconProps: SYSTEM_COLUMN_ICON
  },
  {
    key: 'system-classname',
    attribute: 'classname',
    translationKey: 'data-hub.schema.system.classname',
    iconProps: SYSTEM_COLUMN_ICON
  },
  {
    key: 'system-index',
    attribute: 'index',
    translationKey: 'data-hub.schema.system.index',
    iconProps: SYSTEM_COLUMN_ICON
  }
]

// Create a lookup map for quick access by attribute name
export const systemColumnLookup = new Map<string, SystemColumnDefinition>(
  systemColumnDefinitions.map(def => [def.attribute, def])
)
