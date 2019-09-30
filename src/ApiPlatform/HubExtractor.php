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

namespace Pimcore\Bundle\DataHubBundle\ApiPlatform;

use Pimcore\Model\DataObject\Concrete;

class HubExtractor
{
    public function extractConfigFromEntity($entityConfig, Concrete $entity)
    {
        $result = [];

        foreach ($entityConfig['columnConfig']['columns'] as $column) {
            //NOT SUPPORTED YET!
            if ($column['isOperator']) {
                continue;
            }

            $result[$column['attributes']['label']] = $entity->getValueForFieldName($column['attributes']['attribute']);
        }

        $result['id'] = $entity->getId();

        return $result;
    }
}
