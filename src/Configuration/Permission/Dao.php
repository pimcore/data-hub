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
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace Pimcore\Bundle\DataHubBundle\Configuration\Permission;

use Pimcore\Model;

class Dao extends Model\Dao\AbstractDao
{
    const TABLE_NAME = 'plugin_datahub_permissions';

    public function save()
    {
        $data = [];

        $dataRaw = $this->model->getObjectVars();
        foreach ($dataRaw as $key => $value) {
            if (in_array($key, $this->getValidTableColumns(self::TABLE_NAME))) {
                if (is_bool($value)) {
                    $value = (int)$value;
                }

                $data[$key] = $value;
            }
        }
        $this->db->insert(self::TABLE_NAME, $data);
    }
}
