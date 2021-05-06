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

namespace Pimcore\Bundle\DataHubBundle\Configuration\Workspace;

use Pimcore\Model\AbstractModel;

/**
 * @method \Pimcore\Bundle\DataHubBundle\Configuration\Workspace\Dao getDao()
 */
class AbstractWorkspace extends AbstractModel
{
    /**
     * @var string
     */
    public $configuration;

    /**
     * @var int
     */
    public $cid;

    /**
     * @var string
     */
    public $cpath;

    /**
     * @var bool
     */
    public $create = false;

    /**
     * @var bool
     */
    public $read = false;

    /**
     * @var bool
     */
    public $update = false;

    /**
     * @var bool
     */
    public $delete = false;

    /**
     * @return string
     */
    public function getConfiguration(): string
    {
        return $this->configuration;
    }

    /**
     * @param string $configuration
     */
    public function setConfiguration(string $configuration): void
    {
        $this->configuration = $configuration;
    }

    /**
     * @return int
     */
    public function getCid(): int
    {
        return $this->cid;
    }

    /**
     * @param int $cid
     */
    public function setCid(int $cid): void
    {
        $this->cid = $cid;
    }

    /**
     * @return string
     */
    public function getCpath(): string
    {
        return $this->cpath;
    }

    /**
     * @param string $cpath
     */
    public function setCpath(string $cpath): void
    {
        $this->cpath = $cpath;
    }

    /**
     * @return bool
     */
    public function isCreate(): bool
    {
        return $this->create;
    }

    /**
     * @param bool $create
     */
    public function setCreate(bool $create): void
    {
        $this->create = $create;
    }

    /**
     * @return bool
     */
    public function getRead()
    {
        return $this->read;
    }

    /**
     * @param bool $read
     */
    public function setRead(bool $read): void
    {
        $this->read = $read;
    }

    /**
     * @return bool
     */
    public function isUpdate(): bool
    {
        return $this->update;
    }

    /**
     * @param bool $update
     */
    public function setUpdate(bool $update): void
    {
        $this->update = $update;
    }

    /**
     * @return bool
     */
    public function isDelete(): bool
    {
        return $this->delete;
    }

    /**
     * @param bool $delete
     */
    public function setDelete(bool $delete): void
    {
        $this->delete = $delete;
    }
}
