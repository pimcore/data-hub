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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DocumentResolver;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\ElementDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\RelationHelper;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Model\Document;

class Hardlink
{
    use ServiceTrait;

    public function __construct()
    {
    }

    /**
     * @param array $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     *
     * @return ElementDescriptor|null
     *
     * @throws \Exception
     */
    public function resolveTarget($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {
        $documentId = $value['id'];
        $document = Document::getById($documentId);

        if ($document instanceof Document\Hardlink) {
            if ($relation = $document->getSourceDocument()) {
                return RelationHelper::processRelation(
                    $relation,
                    $this->getGraphQlService(),
                    $args,
                    $context,
                    $resolveInfo
                );
            }
        }

        return null;
    }
}
