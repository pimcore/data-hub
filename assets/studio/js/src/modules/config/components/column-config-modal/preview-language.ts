/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */
/** The language previews use: the saved one, else the user's first content language, else English. Shared by the editor and the preview bar so both agree. */

export const resolvePreviewLanguage = (language: string | undefined, contentLanguages: string[] | undefined): string =>
  language ?? contentLanguages?.[0] ?? 'en'
