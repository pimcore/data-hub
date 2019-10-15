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
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Bundle\DataHubBundle\EventListener;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Pimcore\Event\AssetEvents;
use Pimcore\Event\DataObjectEvents;
use Pimcore\Event\Model\AssetEvent;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Bundle\DataHubBundle\Configuration\Workspace\Dao;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DataChangeListener implements EventSubscriberInterface
{
    /**
     * @var Connection
     */
    protected $db;

    /**
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            DataObjectEvents::POST_UPDATE => 'onObjectUpdate',
            DataObjectEvents::POST_DELETE => 'onObjectDelete',
            AssetEvents::POST_UPDATE      => 'onAssetUpdate',
            AssetEvents::POST_DELETE      => 'onAssetDelete',
        ];
    }

    /**
     * @param DataObjectEvent $e
     *
     * @throws DBALException
     */
    public function onObjectUpdate(DataObjectEvent $e)
    {
        if (!$e->hasArgument('oldPath')) {
            return;
        }

        $object = $e->getObject();
        $oldPath = $e->getArgument('oldPath');

        $this->db->update(Dao::TABLE_NAME_DATAOBJECT, [
            'cpath' => $object->getRealFullPath()
        ], [
            'cid' => $object->getId()
        ]);

        $command = sprintf(
            'UPDATE %s SET cpath = replace(cpath,%s,%s) WHERE cpath LIKE %s;',
            Dao::TABLE_NAME_DATAOBJECT,
            $this->db->quote($oldPath . '/'),
            $this->db->quote($object->getRealFullPath() . '/'),
            $this->db->quote($oldPath . '/%')
        );

        $this->db->query($command);

    }

    /**
     * @param DataObjectEvent $e
     *
     * @throws DBALException
     */
    public function onObjectDelete(DataObjectEvent $e)
    {
        $object = $e->getObject();
        $this->db->delete(Dao::TABLE_NAME_DATAOBJECT, ['cid' => $object->getId()]);
    }

    /**
     * @param AssetEvent $e
     *
     * @throws DBALException
     */
    public function onAssetUpdate(AssetEvent $e)
    {
        if (!$e->hasArgument('oldPath')) {
            return;
        }

        $oldPath = $e->getArgument('oldPath');
        $asset = $e->getAsset();

        $this->db->update(Dao::TABLE_NAME_ASSET, [
            'cpath' => $asset->getRealFullPath()
        ], [
            'cid' => $asset->getId()
        ]);

        $command = sprintf(
            'UPDATE %s SET cpath = replace(cpath,%s,%s) WHERE cpath LIKE %s;',
            Dao::TABLE_NAME_ASSET,
            $this->db->quote($oldPath . '/'),
            $this->db->quote($asset->getRealFullPath() . '/'),
            $this->db->quote($oldPath . '/%')
        );

        $this->db->query($command);

    }

    /**
     * @param AssetEvent $e
     *
     * @throws DBALException
     */
    public function onAssetDelete(AssetEvent $e)
    {
        $asset = $e->getAsset();
        $this->db->delete(Dao::TABLE_NAME_ASSET, ['cid' => $asset->getId()]);
    }

}
