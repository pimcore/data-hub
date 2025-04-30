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
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData;

/**
 * @internal
 */
final class QuantityValue extends Base
{
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
        Service::setValue($object, $attribute, function ($container, $setter) use ($newValue) {
            if ($newValue) {
                $value = null;
                $unit = null;
                if (isset($newValue['value'])) {
                    $value = $newValue['value'];
                }
                if (isset($newValue['unitId'])) {
                    $unit = \Pimcore\Model\DataObject\QuantityValue\Unit::getById($newValue['unitId']);
                } elseif (isset($newValue['unit'])) {
                    $unit = \Pimcore\Model\DataObject\QuantityValue\Unit::getByAbbreviation($newValue['unit']);
                }
                $quantityValue = new \Pimcore\Model\DataObject\Data\QuantityValue($value, $unit);

                return $container->$setter($quantityValue);
            }

            return null;
        });
    }
}
