/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

export interface QueryEntity {
  id: string
  entity: string
  [key: string]: any
}

export interface MutationEntity {
  id: string
  entity: string
  create: boolean
  update: boolean
  delete: boolean
  [key: string]: any
}

export interface GenericType {
  name: string
  readPossible: boolean
  createPossible: boolean
  updatePossible: boolean
  deletePossible: boolean
  read: boolean
  create: boolean
  update: boolean
  delete: boolean
}

export type SchemaType = 'query' | 'mutation' | 'genericTypes'
