<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace Pimcore\Bundle\DataHubBundle\EventListener;

use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\WorkspaceHelper;
use Pimcore\Event\AssetEvents;
use Pimcore\Event\DataObjectEvents;
use Pimcore\Event\DocumentEvents;
use Pimcore\Event\Model\AssetEvent;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Event\Model\DocumentEvent;
use Pimcore\Model\Element\ValidationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DataChangeListener implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            DataObjectEvents::POST_UPDATE => 'onObjectUpdate',
            DataObjectEvents::POST_DELETE => 'onObjectDelete',
            DocumentEvents::POST_UPDATE => 'onDocumentUpdate',
            DocumentEvents::POST_DELETE => 'onDocumentDelete',
            AssetEvents::POST_UPDATE => 'onAssetUpdate',
            AssetEvents::POST_DELETE => 'onAssetDelete',
        ];
    }

    /**
     * @param DataObjectEvent $e
     *
     * @throws ValidationException
     */
    public function onObjectUpdate(DataObjectEvent $e)
    {
        if (!$e->hasArgument('oldPath')) {
            return;
        }

        $object = $e->getObject();
        $oldPath = $e->getArgument('oldPath');

        $this->checkConfiguration(WorkspaceHelper::MODIFY_SPACE_OBJECT, WorkspaceHelper::MODIFY_TYPE_REPLACE, $oldPath, $object->getRealFullPath());
    }

    /**
     * @param DataObjectEvent $e
     *
     * @throws ValidationException
     */
    public function onObjectDelete(DataObjectEvent $e)
    {
        $object = $e->getObject();

        $this->checkConfiguration(WorkspaceHelper::MODIFY_SPACE_OBJECT, WorkspaceHelper::MODIFY_TYPE_DELETE, $object->getRealFullPath(), null);
    }

    /**
     * @param DocumentEvent $e
     *
     * @throws ValidationException
     */
    public function onDocumentUpdate(DocumentEvent $e)
    {
        if (!$e->hasArgument('oldPath')) {
            return;
        }

        $document = $e->getDocument();
        $oldPath = $e->getArgument('oldPath');

        $this->checkConfiguration(WorkspaceHelper::MODIFY_SPACE_DOCUMENT, WorkspaceHelper::MODIFY_TYPE_REPLACE, $oldPath, $document->getRealFullPath());
    }

    /**
     * @param DocumentEvent $e
     *
     * @throws ValidationException
     */
    public function onDocumentDelete(DocumentEvent $e)
    {
        $object = $e->getDocument();

        $this->checkConfiguration(WorkspaceHelper::MODIFY_SPACE_DOCUMENT, WorkspaceHelper::MODIFY_TYPE_DELETE, $object->getRealFullPath(), null);
    }

    /**
     * @param AssetEvent $e
     *
     * @throws ValidationException
     */
    public function onAssetUpdate(AssetEvent $e)
    {
        if (!$e->hasArgument('oldPath')) {
            return;
        }

        $asset = $e->getAsset();
        $oldPath = $e->getArgument('oldPath');

        $this->checkConfiguration(WorkspaceHelper::MODIFY_SPACE_ASSET, WorkspaceHelper::MODIFY_TYPE_REPLACE, $oldPath, $asset->getRealFullPath());
    }

    /**
     * @param AssetEvent $e
     *
     * @throws ValidationException
     */
    public function onAssetDelete(AssetEvent $e)
    {
        $asset = $e->getAsset();

        $this->checkConfiguration(WorkspaceHelper::MODIFY_SPACE_ASSET, WorkspaceHelper::MODIFY_TYPE_DELETE, $asset->getRealFullPath(), null);
    }

    /**
     * @param string      $dataType
     * @param string      $modificationType
     * @param string      $searchValue
     * @param string|null $replaceValue
     *
     * @throws ValidationException
     */
    protected function checkConfiguration($dataType, $modificationType, $searchValue, $replaceValue)
    {
        $configList = Configuration::getList();

        if (!is_array($configList)) {
            return;
        }

        foreach ($configList as $configurationEntity) {
            try {
                $entity = WorkspaceHelper::modifyWorkspaceRowByType($configurationEntity, $dataType, $modificationType, $searchValue, $replaceValue);
            } catch (\Throwable $e) {
                throw new ValidationException(sprintf('Could not modify workspace row: %s', $e->getMessage()), 0, $e);
            }

            if (!$entity instanceof Configuration) {
                continue;
            }

            try {
                $entity->save();
            } catch (\Throwable $e) {
                throw new ValidationException(sprintf('Could not save configuration: %s', $e->getMessage()), 0, $e);
            }
        }
    }
}
