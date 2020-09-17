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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectInputProcessor;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\Exception\ClientSafeException;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Model\DataObject\Concrete;


class ManyToManyRelation extends Base
{

    /**
     * @param Concrete $object
     * @param $newValue
     * @param $args
     * @param array $context
     * @param ResolveInfo $info
     * @throws \Exception
     */
    public function process(Concrete $object, $newValue, $args, $context, ResolveInfo $info)
    {
        $attribute = $this->getAttribute();
        Service::setValue($object, $attribute, function($container, $setter) use ($newValue) {
            $result = [];
            if (is_array($newValue)) {
                foreach ($newValue as $newValueItemKey => $newValueItemValue) {
                    if (!isset($newValueItemValue["type"])) {
                        throw new ClientSafeException("type expected");
                    }

                    if (!isset($newValueItemValue["id"])) {
                        throw new ClientSafeException("ID expected");
                    }

                    $element = \Pimcore\Model\Element\Service::getElementById($newValueItemValue["type"], $newValueItemValue["id"]);
                    if ($element) {
                        $result[] = $element;
                    }
                }
            }

            return $container->$setter($result);
        });
    }
}

