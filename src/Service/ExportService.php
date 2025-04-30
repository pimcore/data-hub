<?php

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\DataHubBundle\Service;

use Pimcore\Bundle\DataHubBundle\Configuration;

/**
 * @internal
 */
final class ExportService
{
    public function exportConfigurationJson(Configuration $configuration): string
    {
        $configuration = clone $configuration;
        $data = json_decode(json_encode($configuration));

        unset(
            $data->configuration->general->modificationDate,
            $data->configuration->general->createDate,
        );

        return json_encode($data, JSON_PRETTY_PRINT);
    }
}
