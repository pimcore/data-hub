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
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\FieldConfigGenerator;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\Type\AssetType;
use Pimcore\Bundle\DataHubBundle\PimcoreDataHubBundle;
use Pimcore\Bundle\DataHubBundle\WorkspaceHelper;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Concrete;

class AssetBase extends Base
{
    public function getGraphQlFieldConfig(Data $fieldDefinition, $class = null, $container = null)
    {
        return $this->enrichConfig([
            'name' => $fieldDefinition->getName(),
            'type' => $this->getFieldType($fieldDefinition, $class, $container),
            'resolve' => $this->getResolver($fieldDefinition, $class)
        ], $container);
    }

    /**
     * @param Data $fieldDefinition
     * @param null $class
     * @param null $container
     *
     * @return \GraphQL\Type\Definition\ListOfType|mixed|AssetType
     */
    public function getFieldType(Data $fieldDefinition, $class = null, $container = null)
    {
        return AssetType::getInstance();
    }

    /**
     * @param Data $fieldDefinition
     * @param $class
     *
     * @return \Closure
     */
    public function getResolver($fieldDefinition, $class)
    {
        return function ($value = null, $args = [], $context = [], ResolveInfo $resolveInfo = null) use (
            $fieldDefinition,
            $class
        ) {
            $containerObjectId = $value['id'];
            $o = Concrete::getById($containerObjectId);
            if ($o) {
                $getter = 'get' . ucfirst($fieldDefinition->getName());
                $asset = $o->$getter();
                if (!$asset) {
                    return null;
                }

                $assetElement = $this->getAssetElement($asset);

                if (!WorkspaceHelper::isAllowed($assetElement, $context['configuration'], 'read')) {
                    if (PimcoreDataHubBundle::getNotAllowedPolicy() == PimcoreDataHubBundle::NOT_ALLOWED_POLICY_EXCEPTION) {
                        throw new \Exception('not allowed to view ' . $asset->getFullPath());
                    } else {
                        return null;
                    }
                }

                $data = new \ArrayObject();
                $data->setFlags(\ArrayObject::STD_PROP_LIST | \ArrayObject::ARRAY_AS_PROPS);

                //TODO maybe the right place to inject additional data like "metadata"
                /** @var $fieldHelper \Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper\AbstractFieldHelper */
                $fieldHelper = \Pimcore::getContainer()->get('pimcore.datahub.graphql.fieldhelper.asset');
                $fieldHelper->extractData($data, $assetElement, $args, $context, $resolveInfo);
                $data = $data->getArrayCopy();

                if ($data['data']) {
                    $data['data'] = base64_encode($data['data']);
                }

                return $data;
            }

            return null;
        };
    }

    /** Return the actual asset (AbstractElement)
     * @param $asset
     *
     * @return mixed
     */
    public function getAssetElement($asset)
    {
        return $asset;
    }
}
