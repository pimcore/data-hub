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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectQueryFieldConfigGenerator\Helper;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\ElementDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Bundle\DataHubBundle\PimcoreDataHubBundle;
use Pimcore\Bundle\DataHubBundle\WorkspaceHelper;
use Pimcore\Model\Element\ElementInterface;

class Href
{

    use ServiceTrait;

    /**
     * @var
     */
    public $fieldDefinition;

    /**
     * @var
     */
    public $class;

    /**
     * @var
     */
    public $attribute;


    /**
     * Href constructor.
     * @param \Pimcore\Bundle\DataHubBundle\GraphQL\Service $graphQlService
     * @param $attribute
     * @param $fieldDefinition
     * @param $class
     */
    public function __construct(\Pimcore\Bundle\DataHubBundle\GraphQL\Service $graphQlService, $attribute, $fieldDefinition, $class)
    {
        $this->attribute = $attribute;
        $this->fieldDefinition = $fieldDefinition;
        $this->class = $class;

        $this->setGraphQLService($graphQlService);
    }

    /**
     * @param null $value
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     *
     * @return array|null
     *
     * @throws \Exception
     */
    public function resolve($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null)
    {

        $relation = \Pimcore\Bundle\DataHubBundle\GraphQL\Service::resolveValue($value['id'], $this->fieldDefinition, $this->attribute, $args, $value);

        if ($relation instanceof ElementInterface) {
            if (!WorkspaceHelper::isAllowed($relation, $context['configuration'], 'read')) {
                if (PimcoreDataHubBundle::getNotAllowedPolicy() == PimcoreDataHubBundle::NOT_ALLOWED_POLICY_EXCEPTION) {
                    throw new \Exception('not allowed to view ' . $relation->getFullPath());
                } else {
                    return null;
                }
            }

            $data = new ElementDescriptor($relation);
            $this->getGraphQlService()->extractData($data, $relation, $args, $context, $resolveInfo);
            return $data;
        }
    }
}
