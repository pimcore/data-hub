/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { isAllowed } from '@pimcore/studio-ui-bundle/modules/auth'

const DATA_HUB_ADMIN_PERMISSION = 'plugin_datahub_admin'
const DATA_HUB_ADAPTER_PERMISSION_PREFIX = 'plugin_datahub_adapter_'

/**
 * A user may create/clone/import a configuration of an adapter type when they are a
 * data hub admin or hold the adapter-specific permission for that type. Mirrors the
 * backend create check (ConfigurationService::checkCreatePermission).
 */
export const canCreateAdapter = (adapterId: string): boolean =>
  isAllowed(DATA_HUB_ADMIN_PERMISSION) || isAllowed(`${DATA_HUB_ADAPTER_PERMISSION_PREFIX}${adapterId}`)
