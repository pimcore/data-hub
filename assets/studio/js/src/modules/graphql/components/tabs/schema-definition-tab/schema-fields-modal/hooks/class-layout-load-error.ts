/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

export type ClassLayoutLoadError = 'forbidden' | 'failed'

export interface BrickLayoutLoadError {
  key: string
  error: ClassLayoutLoadError
}

export interface LayoutLoadErrorMessage {
  key: string
  message: string
}

type Translate = (key: string, options?: Record<string, string>) => string

export const toClassLayoutLoadError = (error: unknown): ClassLayoutLoadError | undefined => {
  if (error === undefined || error === null) return undefined
  return (error as { status?: unknown }).status === 403 ? 'forbidden' : 'failed'
}

export const collectBrickLayoutResults = <T>(
  brickKeys: string[],
  responses: Array<{ data?: T, error?: unknown } | undefined>
): { layouts: Map<string, T>, loadErrors: BrickLayoutLoadError[] } => {
  const layouts = new Map<string, T>()
  const loadErrors: BrickLayoutLoadError[] = []

  brickKeys.forEach((key, index) => {
    const data = responses[index]?.data
    const error = toClassLayoutLoadError(responses[index]?.error)
    if (data !== undefined && data !== null) {
      layouts.set(key, data)
    } else if (error !== undefined) {
      loadErrors.push({ key, error })
    }
  })

  return { layouts, loadErrors }
}

export const isBrickFetchPending = ({ classLayoutLoading, brickKeyCount, loadedBrickCount, brickErrorCount }: {
  classLayoutLoading: boolean
  brickKeyCount: number
  loadedBrickCount: number
  brickErrorCount: number
}): boolean => !classLayoutLoading && brickKeyCount > 0 && loadedBrickCount === 0 && brickErrorCount === 0

export const getLayoutLoadErrorMessages = (
  loadError: ClassLayoutLoadError | undefined,
  brickLoadErrors: BrickLayoutLoadError[],
  t: Translate
): LayoutLoadErrorMessage[] => {
  const messages: LayoutLoadErrorMessage[] = []

  if (loadError !== undefined) {
    messages.push({ key: 'class', message: t(`data-hub.schema.class-attributes-load-error.${loadError}`) })
  }

  const bricksByError = new Map<ClassLayoutLoadError, string[]>()
  brickLoadErrors.forEach(({ key, error }) => {
    bricksByError.set(error, [...(bricksByError.get(error) ?? []), key])
  })

  bricksByError.forEach((bricks, error) => {
    messages.push({
      key: `bricks-${error}`,
      message: t(`data-hub.schema.brick-layouts-load-error.${error}`, { bricks: bricks.join(', ') })
    })
  })

  return messages
}
