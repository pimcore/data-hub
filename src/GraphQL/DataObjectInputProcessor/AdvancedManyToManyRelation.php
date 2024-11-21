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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectInputProcessor;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ElementIdentificationTrait;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Data\ElementMetadata;
use Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData;
use Pimcore\Model\Exception\NotFoundException;

class AdvancedManyToManyRelation extends Base
{
    use ElementIdentificationTrait;

    /**
     * @param Concrete|AbstractData $object
     * @param array $newValue
     * @param array $args
     * @param array $context
     *
     * @throws \Exception
     */
    public function process($object, $newValue, $args, $context, ResolveInfo $info)
    {
        $attribute = $this->getAttribute();
        Service::setValue($object, $attribute, function ($container, $setter, $fieldName) use ($newValue) {
            $result = [];
            if (is_array($newValue)) {
                foreach ($newValue as $newValueItemKey => $newValueItemValue) {
                    $element = $this->getElementByTypeAndIdOrPath($newValueItemValue);

                    if ($element) {
                        $data = [];
                        $columns = [];
                        $metaData = $newValueItemValue['metadata'] ?? null;
                        if ($metaData) {
                            foreach ($metaData as $metaDataKey => $metaDataValue) {
                                $columns[] = $metaDataValue['name'];
                                $data[$metaDataValue['name']] = $metaDataValue['value'];
                            }
                        }
                        $item = new ElementMetadata($fieldName, $columns, $element);
                        if ($data !== []) {
                            $item->setData($data);
                        }
                        $result[] = $item;
                    } else {
                        throw new NotFoundException(
                            sprintf('Element with id %s or fullpath %s not found',
                                $newValueItemValue['id'],
                                $newValueItemValue['fullpath']
                            )
                        );
                    }
                }
            }

            return $container->$setter($result);
        });
    }
}
