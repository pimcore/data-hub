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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DocumentResolver;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\RelationHelper;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Model\Document;
use Pimcore\Model\Element\Service;


class Link
{
    use ServiceTrait;

    /**
     * Link constructor.
     * @param \Pimcore\Bundle\DataHubBundle\GraphQL\Service $graphQlService
     */
    public function __construct(\Pimcore\Bundle\DataHubBundle\GraphQL\Service $graphQlService)
    {
        $this->graphQlService = $graphQlService;
    }

    /**
     * @param array $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     * @throws \Exception
     */
    public function resolveObject($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        $documentId = $value['id'];
        $document = Document::getById($documentId);

        if ($document instanceof Document\Link) {
            $relation = $document->getObject();
            if ($relation) {
                return RelationHelper::processRelation($relation, $this->getGraphQlService(), $args, $context, $resolveInfo);
            }
        }

        return null;
    }


    /**
     * @param null $value
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     * @throws \Exception
     */
    public function resolveTarget($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {

        if ($value instanceof Document\Editable\Link) {
            $value = $value->getData();
        }

        if (is_array($value)) {
            $internal = $value["internal"];
            if ($internal) {
                $internalId = $value["internalId"];
                $internalType = $value["internalType"];
                $relation = Service::getElementById($internalType, $internalId);
                if ($relation) {
                    $data = RelationHelper::processRelation($relation, $this->getGraphQlService(), $args, $context, $resolveInfo);
                    return $data;
                }
            }
        }

        return null;
    }
}

