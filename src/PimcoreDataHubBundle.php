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

namespace Pimcore\Bundle\DataHubBundle;

use Pimcore\Bundle\AdminBundle\PimcoreAdminBundle;
use Pimcore\Bundle\DataHubBundle\DependencyInjection\Compiler\CustomDocumentTypePass;
use Pimcore\Bundle\DataHubBundle\DependencyInjection\Compiler\ImportExportLocatorsPass;
use Pimcore\Bundle\DataHubBundle\DependencyInjection\PimcoreDataHubExtension;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Installer\InstallerInterface;
use Pimcore\Extension\Bundle\PimcoreBundleAdminClassicInterface;
use Pimcore\Extension\Bundle\Traits\BundleAdminClassicTrait;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;
use Pimcore\HttpKernel\Bundle\DependentBundleInterface;
use Pimcore\HttpKernel\BundleCollection\BundleCollection;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @internal
 */
final class PimcoreDataHubBundle extends AbstractPimcoreBundle implements PimcoreBundleAdminClassicInterface, DependentBundleInterface
{
    use BundleAdminClassicTrait;
    use PackageVersionTrait;

    const RUNTIME_CONTEXT_KEY = 'datahub_context';

    const NOT_ALLOWED_POLICY_EXCEPTION = 1;

    const NOT_ALLOWED_POLICY_NULL = 2;

    //TODO decide whether we want to return null here or throw an exception (maybe make this configurable?)
    public static $notAllowedPolicy = self::NOT_ALLOWED_POLICY_NULL;

    public function getContainerExtension(): ExtensionInterface
    {
        return new PimcoreDataHubExtension();
    }

    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ImportExportLocatorsPass());
        $container->addCompilerPass(new CustomDocumentTypePass());
    }

    public static function registerDependentBundles(BundleCollection $collection): void
    {
        $collection->addBundle(new PimcoreAdminBundle(), 60);
    }

    protected function getComposerPackageName(): string
    {
        return 'pimcore/data-hub';
    }

    public function getCssPaths(): array
    {
        return [];
    }

    public function getJsPaths(): array
    {
        return [];
    }

    /**
     * If the bundle has an installation routine, an installer is responsible of handling installation related tasks
     *
     */
    public function getInstaller(): ?InstallerInterface
    {
        return $this->container->get(Installer::class);
    }

    /**
     * @return int
     */
    public static function getNotAllowedPolicy()
    {
        return self::$notAllowedPolicy;
    }

    /**
     * @param mixed $notAllowedPolicy
     */
    public static function setNotAllowedPolicy($notAllowedPolicy): void
    {
        self::$notAllowedPolicy = $notAllowedPolicy;
    }
}
