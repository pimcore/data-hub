/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import { useCallback } from 'react'
import { useTranslation } from '@pimcore/studio-ui-bundle/app'
import { kebabCase } from 'lodash'
import { type DynamicTypeOperatorAbstract } from '../dynamic-type-operator-abstract'

interface UseOperatorReturn {
  getLocalizedName: (operator: DynamicTypeOperatorAbstract) => string
  getGroup: (operator: DynamicTypeOperatorAbstract) => { group: string, subGroup?: string }
}

export function useOperator (): UseOperatorReturn {
  const { t } = useTranslation()

  const getLocalizedName = useCallback(
    (operator: DynamicTypeOperatorAbstract) => t(`data-hub.operator.${kebabCase(operator.id)}`),
    [t]
  )

  const getGroup = useCallback(
    (operator: DynamicTypeOperatorAbstract) => {
      const group = operator.getGroup()
      const subGroup = operator.getSubGroup()
      return {
        group: t(`data-hub.operator.group.${kebabCase(group)}`),
        subGroup: subGroup !== undefined ? t(`data-hub.operator.subgroup.${kebabCase(subGroup)}`) : undefined
      }
    },
    [t]
  )

  return {
    getLocalizedName,
    getGroup
  }
}
