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

namespace Pimcore\Bundle\DataHubBundle\Model;

class SpecialEntitySetting implements \JsonSerializable
{
    protected $name;

    protected $readPossible = false;
    protected $createPossible = false;
    protected $updatePossible = false;
    protected $deletePossible = false;

    protected $readAllowed = false;
    protected $createAllowed = false;
    protected $updateAllowed = false;
    protected $deleteAllowed = false;

    public function __construct(
        string $name,
        bool $readPossible,
        bool $createPossible,
        bool $updatePossible,
        bool $deletePossible,
        bool $readAllowed,
        bool $createAllowed,
        bool $updateAllowed,
        bool $deleteAllowed
    ) {
        $this->name = $name;
        $this->readPossible = $readPossible;
        $this->createPossible = $createPossible;
        $this->updatePossible = $updatePossible;
        $this->deletePossible = $deletePossible;
        $this->readAllowed = $readAllowed;
        $this->createAllowed = $createAllowed;
        $this->updateAllowed = $updateAllowed;
        $this->deleteAllowed = $deleteAllowed;
    }

    /**
     * @return array
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'name' => $this->name,
            'readPossible' => $this->isReadPossible(),
            'createPossible' => $this->isCreatePossible(),
            'updatePossible' => $this->isUpdatePossible(),
            'deletePossible' => $this->isDeletePossible(),

            'readAllowed' => $this->isReadAllowed(),
            'createAllowed' => $this->isCreateAllowed(),
            'updateAllowed' => $this->isUpdateAllowed(),
            'deleteAllowed' => $this->isDeleteAllowed(),
        ];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function isReadPossible(): bool
    {
        return $this->readPossible;
    }

    public function isCreatePossible(): bool
    {
        return $this->createPossible;
    }

    public function isUpdatePossible(): bool
    {
        return $this->updatePossible;
    }

    public function isDeletePossible(): bool
    {
        return $this->deletePossible;
    }

    public function isReadAllowed(): bool
    {
        return $this->readAllowed;
    }

    public function isCreateAllowed(): bool
    {
        return $this->createAllowed;
    }

    public function isUpdateAllowed(): bool
    {
        return $this->updateAllowed;
    }

    public function isDeleteAllowed(): bool
    {
        return $this->deleteAllowed;
    }
}
