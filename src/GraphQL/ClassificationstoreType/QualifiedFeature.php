<?php declare(strict_types=1);

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreType;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreFeatureType\Helper;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreFeatureType\QualifiedValueType;
use Pimcore\Bundle\DataHubBundle\GraphQL\FeatureDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;

/**
 * A key of a group with all of its qualified values, published beside the unqualified `features`.
 *
 * @internal
 */
final class QualifiedFeature extends ObjectType
{
    public function __construct(Service $graphQlService)
    {
        $fields = Helper::getCommonFields();
        $fields['values'] = [
            'type' => Type::listOf(QualifiedValueType::getInstance($graphQlService)),
            'resolve' => static function ($value = null) {
                if ($value instanceof FeatureDescriptor) {
                    return QualifiedValueReader::qualifiedEntries($value);
                }
            },
        ];

        parent::__construct(['name' => 'csQualifiedFeature', 'fields' => $fields]);
    }
}
