/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { ApiError, trackError } from '@pimcore/studio-ui-bundle/modules/app'

const VALIDATION_FAILED_ERROR_KEY = 'error_validation_failed'

/**
 * Displays an API error for data hub config operations.
 *
 * For validation errors (error_validation_failed) the backend returns a specific, human-readable
 * message (e.g. a modification conflict). Show that message instead of the generic "validation
 * failed" translation that ApiError derives from the error key. All other errors fall back to the
 * default ApiError handling. Content is routed through trackError so duplicate reports within the
 * same cycle collapse into a single dialog.
 */
export function trackConfigError (error: unknown): void {
  const details = (error as { data?: { errorKey?: string, message?: string } } | null | undefined)?.data
  const message = details?.message

  if (details?.errorKey === VALIDATION_FAILED_ERROR_KEY && typeof message === 'string' && message !== '') {
    trackError({ getContent: () => message })

    return
  }

  trackError(new ApiError(error as ConstructorParameters<typeof ApiError>[0]))
}
