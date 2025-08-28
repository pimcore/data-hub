<?php

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectInputProcessor;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ElementIdentificationTrait;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Data\ObjectMetadata;
use Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData;
use Pimcore\Model\Exception\NotFoundException;

/**
 * @internal
 */
final class AdvancedManyToManyObjectRelation extends Base
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
                    $columns = [];
                    $element = $this->getElementByTypeAndIdOrPath($newValueItemValue);

                    if ($element) {
                        $data = [];
                        $metaData = $newValueItemValue['metadata'] ?? null;
                        if ($metaData) {
                            foreach ($metaData as $metaDataKey => $metaDataValue) {
                                $columns[] = $metaDataValue['name'];
                                $data[$metaDataValue['name']] = $metaDataValue['value'];
                            }
                        }
                        $concrete = Concrete::getById($element->getId());
                        $item = new ObjectMetadata($fieldName, $columns, $concrete);
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
