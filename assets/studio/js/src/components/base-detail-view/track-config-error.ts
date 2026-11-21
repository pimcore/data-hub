/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { ApiError, trackError, isApiErrorData, type IApiErrorDetails } from '@pimcore/studio-ui-bundle/modules/app'
import { isEmpty } from 'lodash'

// Mirrors HttpResponseErrorKeys::VALIDATION_FAILED in the studio-backend-bundle.
const VALIDATION_FAILED_ERROR_KEY = 'error_validation_failed'

/**
 * Tracks an API error coming from a Data Hub config load/save.
 *
 * For a generic validation failure the backend returns the human-readable reason
 * in `message` (e.g. "Please define a schema for the export"), while the default
 * ApiError handling would only show the translated "Validation failed" error key
 * and discard that reason. In that case we surface the message instead so the user
 * sees what is actually wrong.
 */
export const trackConfigError = (error: unknown): void => {
  if (!isApiErrorData(error)) {
    return
  }

  if ('data' in error) {
    const details = (error as { data?: IApiErrorDetails }).data

    if (details?.errorKey === VALIDATION_FAILED_ERROR_KEY && !isEmpty(details.message)) {
      // Pass the reason as the top-level message so ApiError.getContent surfaces it verbatim.
      trackError(new ApiError({ message: details.message }))
      return
    }
  }

  trackError(new ApiError(error))
}
