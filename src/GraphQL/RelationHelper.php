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

namespace Pimcore\Bundle\DataHubBundle\GraphQL;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Model\Element\ElementInterface;

/**
 * @internal
 */
final class RelationHelper
{
    /**
     * @param array $args
     * @param array $context
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
