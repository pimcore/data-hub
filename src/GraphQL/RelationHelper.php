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

namespace Pimcore\Bundle\DataHubBundle\GraphQL;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Document;
use Pimcore\Model\Element\ElementInterface;

class RelationHelper
{

    /**
     * @param ElementInterface $relation
     * @param Service $graphQlService
     * @param $args
     * @param $context
     * @param ResolveInfo $resolveInfo
     * @return ElementDescriptor
     */
    public static function processRelation(ElementInterface $relation, Service $graphQlService, $args, $context, ResolveInfo $resolveInfo) {
        $data = new ElementDescriptor();
        $fieldHelper = $graphQlService->getObjectFieldHelper();
        $fieldHelper->extractData($data, $relation, $args, $context, $resolveInfo);

        $type = \Pimcore\Model\Element\Service::getType($relation);
        if ($relation instanceof Concrete) {
            $subtype = $relation->getClass()->getName();
            $data['__elementType'] = $type;
            $data['__elementSubtype'] = $subtype;
        } elseif ($relation instanceof Asset) {
            $data['data'] = $data['data'] ? base64_encode($data['data']) : null;
            $data['__elementType'] = 'asset';
            $data['__elementSubtype'] = $relation->getType();
        } else if ($relation instanceof Document) {
            $data['id'] = $relation->getId();
            $data['__elementType'] = $type;
        }
        return $data;
    }
}
