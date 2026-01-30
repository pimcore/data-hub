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
import { Select, type SelectProps } from '@pimcore/studio-ui-bundle/components'
import { useBundleDataHubThumbnailsCollectionQuery } from '../thumbnails-api-slice.gen'

interface ThumbnailSelectProps extends Omit<SelectProps, 'options'> {}

export const ThumbnailSelect = ({ ...props }: ThumbnailSelectProps): React.JSX.Element => {
  const { data: thumbnailsData } = useBundleDataHubThumbnailsCollectionQuery()

  const thumbnailOptions = thumbnailsData?.items.map((thumbnail) => ({
    label: thumbnail.text,
    value: thumbnail.id
  })) ?? []

  return (
    <Select
      options={ thumbnailOptions }
      showSearch
      style={ { width: '100%' } }
      { ...props }
    />
  )
}
