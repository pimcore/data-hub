<?php

if(\Pimcore\Version::getMajorVersion() >= 11) {
    return [
        \Pimcore\Bundle\AdminBundle\PimcoreAdminBundle::class => ['all' => true],
        \Pimcore\Bundle\DataHubBundle\PimcoreDataHubBundle::class => ['all' => true]
    ];
}

return [
    \Pimcore\Bundle\DataHubBundle\PimcoreDataHubBundle::class => ['all' => true]
];
