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

namespace Pimcore\Bundle\DataHubBundle\GraphQL;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Model\Element\ElementInterface;

class RelationHelper
{
    /**
     * @param ElementInterface $relation
     * @param Service $graphQlService
     * @param array $args
     * @param array $context
     * @param ResolveInfo $resolveInfo
     *
     * @return ElementDescriptor
     */
    public static function processRelation(ElementInterface $relation, Service $graphQlService, $args, $context, ResolveInfo $resolveInfo)
    {
        $data = new ElementDescriptor($relation);
        $graphQlService->extractData($data, $relation, $args, $context, $resolveInfo);

        return $data;
    }
}
