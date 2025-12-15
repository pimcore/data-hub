<?php
declare(strict_types=1);

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\DataHubBundle\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\RouteCollection;

/**
 * @internal
 */
final class StudioRouteLoader extends Loader
{
    private bool $loaded = false;

    public function __construct(private readonly KernelInterface $kernel)
    {
        parent::__construct();
    }

    public function load(mixed $resource, ?string $type = null): RouteCollection
    {
        $routes = new RouteCollection();

        if ($this->loaded) {
            return $routes;
        }

        $bundles = $this->kernel->getBundles();
        if (isset($bundles['PimcoreStudioBackendBundle'])) {
            $imported = $this->import(
                '@PimcoreDataHubBundle/Resources/config/pimcore/studio_routing.yaml',
                'yaml'
            );
            $routes->addCollection($imported);
        }
        $this->loaded = true;

        return $routes;
    }

    public function supports(mixed $resource, ?string $type = null): bool
    {
        return 'pimcore_data_hub_studio' === $type;
    }
}
