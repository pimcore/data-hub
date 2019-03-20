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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\FieldConfigGenerator\Helper;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\PimcoreDataHubBundle;
use Pimcore\Bundle\DataHubBundle\WorkspaceHelper;
use Pimcore\Model\DataObject\Concrete;

class AssetBase
{
    /**
     * @var
     */
    public $fieldDefinition;

    /**
     * @var
     */
    public $class;

    /**
     * Objects constructor.
     *
     * @param $fieldDefinition
     * @param $class
     */
    public function __construct($fieldDefinition, $class)
    {
        $this->fieldDefinition = $fieldDefinition;
        $this->class = $class;
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
        $containerObjectId = $value['id'];
        $o = Concrete::getById($containerObjectId);
        if ($o) {
            $getter = 'get' . ucfirst($this->fieldDefinition->getName());
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

            /** @var $fieldHelper \Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper\AbstractFieldHelper */
            $fieldHelper = \Pimcore::getContainer()->get('pimcore.datahub.graphql.fieldhelper.asset');
            $fieldHelper->extractData($data, $assetElement, $args, $context, $resolveInfo);

            $data['id'] = $assetElement->getId();
            $data['__elementType'] = "asset";
            if ($data['data']) {
                $data['data'] = base64_encode($data['data']);
            }

            return $data;
        }

        return null;
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
