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
 * @category   Pimcore
 * @package    User
 *
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
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
    public $read = false;

    /**
     * @var bool
     */
    public $write = false;

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
    public function getWrite()
    {
        return $this->write;
    }

    /**
     * @param bool $write
     */
    public function setWrite(bool $write): void
    {
        $this->write = $write;
    }
}
