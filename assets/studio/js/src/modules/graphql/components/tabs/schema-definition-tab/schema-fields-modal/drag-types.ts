/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { type DragAndDropInfo } from '@pimcore/studio-ui-bundle/components'

export enum DragType {
  CLASS_ATTRIBUTE = 'class-attribute',
  OPERATOR = 'operator',
  TREE_ITEM = 'tree-item'
}

export enum DropPosition {
  BEFORE = 'before',
  AFTER = 'after',
  INTO = 'into'
}

export interface DragInfo extends DragAndDropInfo {
  type: DragType.CLASS_ATTRIBUTE | DragType.OPERATOR | DragType.TREE_ITEM
  data: {
    key?: string
    title?: string
    dataType?: string
    operatorId?: string
    [key: string]: any
  }
}
