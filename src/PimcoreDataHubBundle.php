<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Bundle\DataHubBundle;

use Pimcore\Bundle\DataHubBundle\DependencyInjection\Compiler\ImportExportLocatorsPass;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Installer\InstallerInterface;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PimcoreDataHubBundle extends AbstractPimcoreBundle
{
    use PackageVersionTrait;

    const RUNTIME_CONTEXT_KEY = 'datahub_context';

    const NOT_ALLOWED_POLICY_EXCEPTION = 1;

    const NOT_ALLOWED_POLICY_NULL = 2;

    //TODO decide whether we want to return null here or throw an exception (maybe make this configurable?)
    public static $notAllowedPolicy = self::NOT_ALLOWED_POLICY_NULL;

    /**
     * @inheritDoc
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ImportExportLocatorsPass());
    }

    /**
     * {@inheritdoc}
     */
    protected function getComposerPackageName(): string
    {
        return 'pimcore/data-hub';
    }

    /**
     * @return array
     */
    public function getCssPaths()
    {
        return [
            '/bundles/pimcoredatahub/css/icons.css',
            '/bundles/pimcoredatahub/css/style.css'
        ];
    }

    /**
     * @return array
     */
    public function getJsPaths()
    {
        return [
            '/bundles/pimcoredatahub/js/datahub.js',
            '/bundles/pimcoredatahub/js/config.js',
            '/bundles/pimcoredatahub/js/adapter/graphql.js',
            '/bundles/pimcoredatahub/js/configuration/graphql/configItem.js',
            '/bundles/pimcoredatahub/js/fieldConfigDialog.js',
            '/bundles/pimcoredatahub/js/Abstract.js',
            '/bundles/pimcoredatahub/js/mutationvalue/DefaultValue.js',
            '/bundles/pimcoredatahub/js/queryvalue/DefaultValue.js',
            '/bundles/pimcoredatahub/js/Abstract.js',
            '/bundles/pimcoredatahub/js/queryoperator/Alias.js',
            '/bundles/pimcoredatahub/js/queryoperator/Concatenator.js',
            '/bundles/pimcoredatahub/js/queryoperator/DateFormatter.js',
            '/bundles/pimcoredatahub/js/queryoperator/ElementCounter.js',
            '/bundles/pimcoredatahub/js/queryoperator/Text.js',
            '/bundles/pimcoredatahub/js/queryoperator/Merge.js',
            '/bundles/pimcoredatahub/js/queryoperator/Substring.js',
            '/bundles/pimcoredatahub/js/queryoperator/Thumbnail.js',
            '/bundles/pimcoredatahub/js/queryoperator/ThumbnailHtml.js',
            '/bundles/pimcoredatahub/js/queryoperator/TranslateValue.js',
            '/bundles/pimcoredatahub/js/queryoperator/Trimmer.js',
            '/bundles/pimcoredatahub/js/mutationoperator/IfEmpty.js',
            '/bundles/pimcoredatahub/js/mutationoperator/LocaleSwitcher.js',
            '/bundles/pimcoredatahub/js/workspace/abstract.js',
            '/bundles/pimcoredatahub/js/workspace/document.js',
            '/bundles/pimcoredatahub/js/workspace/asset.js',
            '/bundles/pimcoredatahub/js/workspace/object.js'
        ];
    }

    /**
     * If the bundle has an installation routine, an installer is responsible of handling installation related tasks
     *
     * @return InstallerInterface|null
     */
    public function getInstaller()
    {
        return $this->container->get(Installer::class);
    }

    /**
     * @return mixed
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
