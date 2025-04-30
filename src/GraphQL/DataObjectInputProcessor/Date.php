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

use Carbon\Carbon;
use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData;

/**
 * @internal
 */
final class Date extends Base
{
    /**
     * @param Concrete|AbstractData $object
     * @param int|string $newValue
     * @param array $args
     * @param array $context
     *
     * @throws \Exception
     */
    public function process($object, $newValue, $args, $context, ResolveInfo $info)
    {
        $attribute = $this->getAttribute();
        Service::setValue($object, $attribute, function ($container, $setter) use ($newValue) {

            if ($newValue === '') {
                $newValue = null;
            }

            if (!is_null($newValue)) {
                if (!is_numeric($newValue)) {
                    $newValue = strtotime($newValue);
                }
                $newValue = Carbon::createFromTimestamp($newValue, date_default_timezone_get());
            }

            return $container->$setter($newValue);
        });
    }
}
