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

namespace Pimcore\Bundle\DataHubBundle\Webpack;

use Pimcore\Bundle\StudioUiBundle\Webpack\WebpackEntryPointProviderInterface;

/**
 * @internal
 */
if (interface_exists(WebpackEntryPointProviderInterface::class)) {
    final class WebpackEntryPointProvider implements WebpackEntryPointProviderInterface
    {
        public function getEntryPointsJsonLocations(): array
        {
            return glob(__DIR__ . '/../../src/Resources/public/studio/build/*/entrypoints.json');
        }

        public function getEntryPoints(): array
        {
            return ['exposeRemote'];
        }

        public function getOptionalEntryPoints(): array
        {
            return [];
        }
    }
}
