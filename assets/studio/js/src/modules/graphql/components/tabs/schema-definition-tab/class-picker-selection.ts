/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

export const filterClassNames = (classNames: string[], searchValue: string): string[] => {
  const search = searchValue.trim().toLowerCase()
  if (search === '') return classNames
  return classNames.filter(name => name.toLowerCase().includes(search))
}

export const selectClassNames = (selected: string[], toSelect: string[], added: Set<string>): string[] => {
  const result = new Set(selected)
  toSelect.filter(name => !added.has(name)).forEach(name => result.add(name))
  return Array.from(result)
}

export const deselectClassNames = (selected: string[], toDeselect: string[]): string[] => {
  const remove = new Set(toDeselect)
  return selected.filter(name => !remove.has(name))
}

export const orderLike = (names: string[], order: string[]): string[] => {
  const included = new Set(names)
  return order.filter(name => included.has(name))
}
