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

use Pimcore\Model;

class Dao extends Model\Dao\AbstractDao
{
    const TABLE_NAME_ASSET = 'plugin_datahub_workspaces_asset';

    const TABLE_NAME_DOCUMENT = 'plugin_datahub_workspaces_document';

    const TABLE_NAME_DATAOBJECT = 'plugin_datahub_workspaces_object';

    public function save()
    {
        if ($this->model instanceof Asset) {
            $tableName = self::TABLE_NAME_ASSET;
        } elseif ($this->model instanceof Document) {
            $tableName = self::TABLE_NAME_DOCUMENT ;
        } elseif ($this->model instanceof DataObject) {
            $tableName = self::TABLE_NAME_DATAOBJECT;
        } else {
            throw new \Exception('unknown workspace type');
        }

        $data = [];

        // add all permissions
        $dataRaw = $this->model->getObjectVars();
        foreach ($dataRaw as $key => $value) {
            if (in_array($key, $this->getValidTableColumns($tableName))) {
                if (is_bool($value)) {
                    $value = (int) $value;
                }
                if (!class_exists("\Pimcore\Db\Connection")) {
                    $key = $this->db->quoteIdentifier($key);
                }
                $data[$key] = $value;
            }
        }
        $this->db->insert($tableName, $data);
    }
}
