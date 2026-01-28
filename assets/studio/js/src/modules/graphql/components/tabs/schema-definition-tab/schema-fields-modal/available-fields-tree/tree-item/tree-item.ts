/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { type DynamicTypeOperatorRegistry } from '../../../../../../../operators/dynamic-type-operator-registry'
import { createSourceConfigFromAttributes } from '../source-config-utils'

export interface TreeItemAttributes {
  attribute?: string
  label?: string
  dataType?: string
  children?: InternalTreeNode[]
  [key: string]: any
}

export interface InternalTreeNode {
  key: string
  isOperator: boolean
  attributes: TreeItemAttributes
}

export interface TreeItemAction {
  key: string
  icon: string
}

export type TreePath = number[]

export abstract class TreeItem {
  constructor (
    public readonly key: string,
    public readonly attributes: TreeItemAttributes
  ) {}

  abstract get isOperator (): boolean
  abstract canHaveChildren (): boolean
  abstract canAcceptChild (child: TreeItem, isMovingWithinSameParent?: boolean): boolean
  abstract getActions (): TreeItemAction[]
  abstract getChildren (operatorRegistry: DynamicTypeOperatorRegistry): TreeItem[]

  toData (): InternalTreeNode {
    return {
      key: this.key,
      isOperator: this.isOperator,
      attributes: { ...this.attributes }
    }
  }
}

export class FieldDefinitionItem extends TreeItem {
  readonly isOperator = false

  canHaveChildren (): boolean {
    return false
  }

  canAcceptChild (_child: TreeItem, _isMovingWithinSameParent: boolean = false): boolean {
    return false
  }

  getActions (): TreeItemAction[] {
    return [{ key: 'delete', icon: 'trash' }]
  }

  getChildren (_operatorRegistry: DynamicTypeOperatorRegistry): TreeItem[] {
    return []
  }
}

export class OperatorItem extends TreeItem {
  constructor (
    key: string,
    attributes: TreeItemAttributes,
    private readonly operatorRegistry: DynamicTypeOperatorRegistry
  ) {
    super(key, attributes)
  }

  readonly isOperator = true

  private getOperatorType (): any {
    return this.operatorRegistry.getDynamicType(String(this.attributes.class ?? ''), false)
  }

  canHaveChildren (): boolean {
    const operatorType = this.getOperatorType()
    if (operatorType === undefined) return false
    return operatorType.allowsChildren?.() ?? false
  }

  canAcceptChild (child: TreeItem, isMovingWithinSameParent: boolean = false): boolean {
    const operatorType = this.getOperatorType()
    if (operatorType === undefined) return false
    if (!this.canHaveChildren()) return false
    if (isMovingWithinSameParent) return true

    const targetConfig = this.toData()
    const sourceConfig = createSourceConfigFromAttributes(child.attributes, child.isOperator)
    return operatorType.allowChild?.(targetConfig, sourceConfig) ?? true
  }

  getActions (): TreeItemAction[] {
    return [
      { key: 'edit', icon: 'edit' },
      { key: 'delete', icon: 'trash' }
    ]
  }

  getChildren (operatorRegistry: DynamicTypeOperatorRegistry): TreeItem[] {
    const children = this.attributes.children ?? []
    return children.map(child => createTreeItem(child, operatorRegistry))
  }
}

export function createTreeItem (data: InternalTreeNode, operatorRegistry: DynamicTypeOperatorRegistry): TreeItem {
  if (data.isOperator) {
    return new OperatorItem(data.key, data.attributes, operatorRegistry)
  }
  return new FieldDefinitionItem(data.key, data.attributes)
}
