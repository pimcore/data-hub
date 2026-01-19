/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

export interface Permission {
  name: string
  read: boolean
  update: boolean
  delete: boolean
}

export interface RoleItem {
  id: number
  name: string
}

export interface UserItem {
  id: number
  username: string
}
