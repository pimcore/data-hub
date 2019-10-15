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
use Pimcore\Event\DocumentEvents;
use Pimcore\Event\Model\AssetEvent;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Event\Model\DocumentEvent;
use Pimcore\Bundle\DataHubBundle\Configuration\Workspace\Dao;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Model\Element\ValidationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DataChangeListener implements EventSubscriberInterface
{
    const TYPE_OBJECT = 'object';

    const TYPE_ASSET = 'asset';

    const TYPE_DOCUMENT = 'document';

    const DATA_MODIFY = 'modify';

    const DATA_DELETE = 'delete';

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
            DocumentEvents::POST_UPDATE   => 'onDocumentUpdate',
            DocumentEvents::POST_DELETE   => 'onDocumentDelete',
            AssetEvents::POST_UPDATE      => 'onAssetUpdate',
            AssetEvents::POST_DELETE      => 'onAssetDelete',
        ];
    }

    /**
     * @param DataObjectEvent $e
     *
     * @throws DBALException
     * @throws ValidationException
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

        $this->checkConfiguration(self::TYPE_OBJECT, self::DATA_MODIFY, $oldPath, $object->getRealFullPath());
    }

    /**
     * @param DataObjectEvent $e
     *
     * @throws DBALException
     * @throws ValidationException
     */
    public function onObjectDelete(DataObjectEvent $e)
    {
        $object = $e->getObject();
        $this->db->delete(Dao::TABLE_NAME_DATAOBJECT, ['cid' => $object->getId()]);

        $this->checkConfiguration(self::TYPE_OBJECT, self::DATA_DELETE, $object->getRealFullPath(), null);
    }

    /**
     * @param DocumentEvent $e
     *
     * @throws DBALException
     * @throws ValidationException
     */
    public function onDocumentUpdate(DocumentEvent $e)
    {
        if (!$e->hasArgument('oldPath')) {
            return;
        }

        $document = $e->getDocument();
        $oldPath = $e->getArgument('oldPath');

        $this->db->update(Dao::TABLE_NAME_DOCUMENT, [
            'cpath' => $document->getRealFullPath()
        ], [
            'cid' => $document->getId()
        ]);

        $command = sprintf(
            'UPDATE %s SET cpath = replace(cpath,%s,%s) WHERE cpath LIKE %s;',
            Dao::TABLE_NAME_DOCUMENT,
            $this->db->quote($oldPath . '/'),
            $this->db->quote($document->getRealFullPath() . '/'),
            $this->db->quote($oldPath . '/%')
        );

        $this->db->query($command);

        $this->checkConfiguration(self::TYPE_DOCUMENT, self::DATA_MODIFY, $oldPath, $document->getRealFullPath());
    }

    /**
     * @param DocumentEvent $e
     *
     * @throws DBALException
     * @throws ValidationException
     */
    public function onDocumentDelete(DocumentEvent $e)
    {
        $object = $e->getDocument();
        $this->db->delete(Dao::TABLE_NAME_DOCUMENT, ['cid' => $object->getId()]);

        $this->checkConfiguration(self::TYPE_DOCUMENT, self::DATA_DELETE, $object->getRealFullPath(), null);
    }

    /**
     * @param AssetEvent $e
     *
     * @throws DBALException
     * @throws ValidationException
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

        $this->checkConfiguration(self::TYPE_ASSET, self::DATA_MODIFY, $oldPath, $asset->getRealFullPath());
    }

    /**
     * @param AssetEvent $e
     *
     * @throws DBALException
     * @throws ValidationException
     */
    public function onAssetDelete(AssetEvent $e)
    {
        $asset = $e->getAsset();
        $this->db->delete(Dao::TABLE_NAME_ASSET, ['cid' => $asset->getId()]);

        $this->checkConfiguration(self::TYPE_ASSET, self::DATA_DELETE, $asset->getRealFullPath(), null);
    }

    /**
     * @param $dataType
     * @param $modificationType
     * @param $searchValue
     * @param $replaceValue
     *
     * @throws ValidationException
     */
    protected function checkConfiguration($dataType, $modificationType, $searchValue, $replaceValue)
    {
        $configList = Configuration::getList();

        if (!is_array($configList)) {
            return;
        }

        /** @var Configuration $configurationEntity */
        foreach ($configList as $configurationEntity) {

            $changed = false;

            $configuration = $configurationEntity->getConfiguration();
            if (!isset($configuration['workspaces']) || !is_array($configuration['workspaces'])) {
                continue;
            }

            $workspaceConfig = $configuration['workspaces'];
            if (!isset($workspaceConfig[$dataType])) {
                continue;
            }

            $workspaceConfigBlocks = $workspaceConfig[$dataType];
            if (!is_array($workspaceConfigBlocks)) {
                continue;
            }

            foreach ($workspaceConfigBlocks as $blockIndex => &$workspaceConfigBlock) {

                if (!isset($workspaceConfigBlock['cpath'])) {
                    continue;
                }

                $cPath = $workspaceConfigBlock['cpath'];
                $cTrailingPath = sprintf('%s/', $workspaceConfigBlock['cpath']);
                $cTrailingSearchValue = sprintf('%s/', $searchValue);
                $cTrailingReplaceValue = sprintf('%s/', $replaceValue);

                if ($cPath === $searchValue) {

                    // it's the element itself
                    $changed = true;

                    if ($modificationType === self::DATA_MODIFY) {
                        $workspaceConfigBlock['cpath'] = $replaceValue;
                    } elseif ($modificationType === self::DATA_DELETE) {
                        unset($workspaceConfigBlocks[$blockIndex]);
                        $workspaceConfigBlocks = array_values($workspaceConfigBlocks); // remove array keys
                    }

                } elseif (strpos($cTrailingPath, $cTrailingSearchValue) !== false) {

                    // it's a sub element
                    $changed = true;

                    if ($modificationType === self::DATA_MODIFY) {
                        $workspaceConfigBlock['cpath'] = str_replace($cTrailingSearchValue, $cTrailingReplaceValue, $workspaceConfigBlock['cpath']);
                    } elseif ($modificationType === self::DATA_DELETE) {
                        unset($workspaceConfigBlocks[$blockIndex]);
                        $workspaceConfigBlocks = array_values($workspaceConfigBlocks); // remove array keys
                    }
                }
            }

            if ($changed === false) {
                continue;
            }

            $workspaceConfig[$dataType] = $workspaceConfigBlocks;
            $configuration['workspaces'] = $workspaceConfig;

            $configurationEntity->setConfiguration($configuration);

            try {
                $configurationEntity->save();
            } catch (\Throwable $e) {
                throw new ValidationException($e->getMessage(), 0, $e);
            }
        }
    }
}
