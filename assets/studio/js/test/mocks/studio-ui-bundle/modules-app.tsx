/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

export const trackedErrors: unknown[] = []

export class ApiError {
  constructor (public readonly payload: unknown) {}
}

export const trackError = (tracked: unknown): void => { trackedErrors.push(tracked) }

export const isApiErrorData = (): boolean => true
