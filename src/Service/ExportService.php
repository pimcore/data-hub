<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace Pimcore\Bundle\DataHubBundle\Service;

use Pimcore\Bundle\DataHubBundle\Configuration;

class ExportService
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
