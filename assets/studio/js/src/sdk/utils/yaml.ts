/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { parse, stringify } from 'yaml'
import type { DocumentOptions, ParseOptions, SchemaOptions, CreateNodeOptions, ToStringOptions } from 'yaml'

export type YamlStringifyOptions = DocumentOptions & SchemaOptions & ParseOptions & CreateNodeOptions & ToStringOptions

/**
 * Parse a YAML string into a JavaScript value.
 *
 * @throws {YAMLParseError} if the input is not valid YAML
 */
export const parseYaml = (str: string): unknown => {
  return parse(str)
}

/**
 * Stringify a JavaScript value as a YAML document.
 *
 * Defaults to an indent of 2 spaces.
 */
export const stringifyYaml = (value: unknown, options?: YamlStringifyOptions): string => {
  return stringify(value, { indent: 2, ...options })
}

/**
 * Returns true if the given string is valid YAML, false otherwise.
 *
 * Never throws.
 */
export const isValidYaml = (str: string): boolean => {
  try {
    parse(str)
    return true
  } catch {
    return false
  }
}
