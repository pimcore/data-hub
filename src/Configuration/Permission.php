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

namespace Pimcore\Bundle\DataHubBundle\Configuration;

use Pimcore\Model\AbstractModel;

/**
 * @method \Pimcore\Bundle\DataHubBundle\Configuration\Permission\Dao getDao()
 */
class Permission extends AbstractModel
{
    /**
     * @var int
     */
    public $uid;

    /**
     * @var string
     */
    public $uname;

    /**
     * @var string
     */
    public $utype;

    /**
     * @var string
     */
    public $configuration;

    /**
     * @var string
     */
    public $type;

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
     * @return int
     */
    public function getUid(): int
    {
        return $this->uid;
    }

    /**
     * @param int $uid
     */
    public function setUid(int $uid): void
    {
        $this->uid = $uid;
    }

    /**
     * @return string
     */
    public function getUname(): string
    {
        return $this->uname;
    }

    /**
     * @param string $uname
     */
    public function setUname(string $uname): void
    {
        $this->uname = $uname;
    }

    /**
     * @return string
     */
    public function getUtype(): string
    {
        return $this->utype;
    }

    /**
     * @param string $utype
     */
    public function setUtype(string $utype): void
    {
        $this->utype = $utype;
    }

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
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return bool
     */
    public function isRead(): bool
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
