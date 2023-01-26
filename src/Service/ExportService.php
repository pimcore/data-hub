<?php

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
