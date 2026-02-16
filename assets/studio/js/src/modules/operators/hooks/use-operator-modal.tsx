/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { useCallback, useMemo } from 'react'
import { type OperatorConfigModalProps } from '../dynamic-type-operator-abstract'
import { useOperator } from './use-operator'

interface UseOperatorModalReturn<T = any> {
  localizedName: string
  updateAttributes: (values: Partial<T>) => void
  getInitialValues: (additionalDefaults?: Partial<T>) => T
}

export function useOperatorModal<T = any> (
  props: OperatorConfigModalProps<T>
): UseOperatorModalReturn<T> {
  const { operator, config, onApply } = props
  const { getLocalizedName } = useOperator()

  const localizedName = useMemo(
    () => getLocalizedName(operator),
    [getLocalizedName, operator]
  )

  const updateAttributes = useCallback(
    (values: Partial<T>): void => {
      const updatedConfig = {
        ...config,
        attributes: {
          ...config.attributes,
          ...values
        }
      }
      onApply(updatedConfig)
    },
    [config, onApply]
  )

  const getInitialValues = useCallback(
    (additionalDefaults?: Partial<T>): T => {
      const result: T = {
        label: localizedName,
        ...additionalDefaults,
        ...config.attributes
      }
      return result
    },
    [config.attributes, localizedName]
  )

  return {
    localizedName,
    updateAttributes,
    getInitialValues
  }
}
