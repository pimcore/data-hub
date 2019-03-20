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

use Pimcore\Bundle\DataHubBundle\Configuration\Workspace\Dao;
use Pimcore\Bundle\DataHubBundle\DependencyInjection\Compiler\ImportExportLocatorsPass;
use Pimcore\Db;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Installer\InstallerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PimcoreDataHubBundle extends AbstractPimcoreBundle
{
    const RUNTIME_CONTEXT_KEY = 'datahub_context';

    const NOT_ALLOWED_POLICY_EXCEPTION = 1;

    const NOT_ALLOWED_POLICY_NULL = 2;

    //TODO decide whether we want to return null here or throw an exception (maybe make this configurable?)
    public static $notAllowedPolicy = self::NOT_ALLOWED_POLICY_NULL;

    public function boot()
    {
        parent::boot();

        \Pimcore::getEventDispatcher()->addListener(\Pimcore\Event\DataObjectEvents::POST_DELETE, function (\Pimcore\Event\Model\DataObjectEvent $e) {
            $object = $e->getObject();
            $db = Db::get();
            $db->delete(Dao::TABLE_NAME_DATAOBJECT, ['cid' => $object->getId()]);
        });

        \Pimcore::getEventDispatcher()->addListener(\Pimcore\Event\AssetEvents::POST_DELETE, function (\Pimcore\Event\Model\AssetEvent $e) {
            $asset = $e->getAsset();
            $db = Db::get();
            $db->delete(Dao::TABLE_NAME_ASSET, ['cid' => $asset->getId()]);
        });

        // update workspace permission in case the fullpath changes
        \Pimcore::getEventDispatcher()->addListener(\Pimcore\Event\DataObjectEvents::POST_UPDATE, function (\Pimcore\Event\Model\DataObjectEvent $e) {
            if ($e->hasArgument('oldPath')) {
                $object = $e->getObject();
                $oldPath = $e->getArgument('oldPath');
                $db = Db::get();

                $db->update(Dao::TABLE_NAME_DATAOBJECT, [
                    'cpath' => $object->getRealFullPath()
                ], [
                    'cid' => $object->getId()
                ]);

                $db->query('update ' . Dao::TABLE_NAME_DATAOBJECT . ' set cpath = replace(cpath,' . $db->quote($oldPath . '/') . ',' . $db->quote($object->getRealFullPath() . '/') . ') where cpath like ' . $db->quote($oldPath . '/%') . ';');
            }
        });

        \Pimcore::getEventDispatcher()->addListener(\Pimcore\Event\AssetEvents::POST_UPDATE, function (\Pimcore\Event\Model\AssetEvent $e) {
            if ($e->hasArgument('oldPath')) {
                $oldPath = $e->getArgument('oldPath');
                $asset = $e->getAsset();
                $db = Db::get();

                $db->update(Dao::TABLE_NAME_ASSET, [
                    'cpath' => $asset->getRealFullPath()
                ], [
                    'cid' => $asset->getId()
                ]);

                $db->query('update ' . Dao::TABLE_NAME_ASSET . ' set cpath = replace(cpath,' . $db->quote($oldPath . '/') . ',' . $db->quote($asset->getRealFullPath() . '/') . ') where cpath like ' . $db->quote($oldPath . '/%') . ';');
            }
        });
    }

    /**
     * @inheritDoc
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ImportExportLocatorsPass());
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
            '/bundles/pimcoredatahub/js/configItem.js',
            '/bundles/pimcoredatahub/js/fieldConfigDialog.js',
            '/bundles/pimcoredatahub/js/Abstract.js',
            '/bundles/pimcoredatahub/js/value/DefaultValue.js',
            '/bundles/pimcoredatahub/js/Abstract.js',
            '/bundles/pimcoredatahub/js/operator/Concatenator.js',
            '/bundles/pimcoredatahub/js/operator/DateFormatter.js',
            '/bundles/pimcoredatahub/js/operator/ElementCounter.js',
            '/bundles/pimcoredatahub/js/operator/Text.js',
            '/bundles/pimcoredatahub/js/operator/Merge.js',
            '/bundles/pimcoredatahub/js/operator/Substring.js',
            '/bundles/pimcoredatahub/js/operator/Thumbnail.js',
            '/bundles/pimcoredatahub/js/operator/TranslateValue.js',
            '/bundles/pimcoredatahub/js/operator/Trimmer.js',
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
        return new Installer();
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
