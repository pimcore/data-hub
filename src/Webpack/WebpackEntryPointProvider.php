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

use Pimcore\Bundle\StudioUiBundle\Build\BuildArchive;
use Pimcore\Bundle\StudioUiBundle\Build\BuildArchiveExtractionTrait;
use Pimcore\Bundle\StudioUiBundle\Build\BuildArchiveProviderInterface;

/**
 * @internal
 */
final class WebpackEntryPointProvider implements BuildArchiveProviderInterface
{
    use BuildArchiveExtractionTrait;

    /**
     * @return string[]
     */
    public function getEntryPoints(): array
    {
        return ['exposeRemote'];
    }

    /**
     * @return string[]
     */
    public function getOptionalEntryPoints(): array
    {
        return [];
    }

    protected function buildArchive(): BuildArchive
    {
        return new BuildArchive(
            archiveGlob: __DIR__ . '/../../build-dist/build*.zip',
            targetDir: __DIR__ . '/../../src/Resources/public/studio/build',
        );
    }
}
