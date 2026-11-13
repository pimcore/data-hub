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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreFeatureQueryTypeGenerator;

use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreFeatureType\ElementResolver;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreFeatureType\ValueType;

/**
 * @internal
 */
final class ManyToManyRelation extends Base
{
    /**
     * @return ValueType
     *
     * @throws \Exception
     */
    public function getFieldType()
    {
        return ValueType::getInstance(
            'csFeatureManyToManyRelation',
            'elements',
            Type::listOf($this->getGraphQlService()->buildGeneralType('anytarget')),
            [new ElementResolver($this->getGraphQlService()), 'resolveElements']
        );
    }
}
