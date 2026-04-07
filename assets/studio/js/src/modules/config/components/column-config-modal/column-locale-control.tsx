/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

import React from 'react'
import { LanguageSelection } from '@pimcore/studio-ui-bundle/components'
import { useUser } from '@pimcore/studio-ui-bundle/modules/auth'

export interface ColumnLocaleControlProps {
  value: string | null | undefined
  onChange: (locale: string | null) => void
}

export const ColumnLocaleControl = ({ value, onChange }: ColumnLocaleControlProps): React.JSX.Element => {
  const user = useUser()
  const languages: string[] = [
    '-',
    ...(Array.isArray(user.contentLanguages) ? (user.contentLanguages as string[]) : [])
  ]
  const selected = value ?? '-'

  return (
    <LanguageSelection
      languages={ languages }
      onSelectLanguage={ (lang) => { onChange(lang === '-' ? null : lang) } }
      selectedLanguage={ selected }
    />
  )
}
