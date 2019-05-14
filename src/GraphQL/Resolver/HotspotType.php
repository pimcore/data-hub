<?php
declare(strict_types=1);
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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\PimcoreDataHubBundle;
use Pimcore\Bundle\DataHubBundle\WorkspaceHelper;
use Pimcore\Model\Asset;

/**
 * Class HotspotType
 * @package Pimcore\Bundle\DataHubBundle\GraphQL\Resolver
 */
class HotspotType
{
    /**
     * @param null $value
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     * @throws \Exception
     */
    public function resolveImage($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        if ($args && $args['defaultLanguage']) {
            $localeService = \Pimcore::getContainer()->get('pimcore.locale');
            $localeService->setLocale($args['defaultLanguage']);
        }

        $asset = Asset::getById($value['id']);
        if (!$asset instanceof Asset) {
            return null;
        }

        if (!WorkspaceHelper::isAllowed($asset, $context['configuration'], 'read')) {
            if (PimcoreDataHubBundle::getNotAllowedPolicy() == PimcoreDataHubBundle::NOT_ALLOWED_POLICY_EXCEPTION) {
                throw new \Exception('not allowed to view asset ' . $asset->getFullPath());
            } else {
                return null;
            }
        }

        $data = new \ArrayObject(['data' => null]);
        $data->setFlags(\ArrayObject::STD_PROP_LIST | \ArrayObject::ARRAY_AS_PROPS);

        $fieldHelper = \Pimcore::getContainer()->get('pimcore.datahub.graphql.fieldhelper.asset');
        $fieldHelper->extractData($data, $asset, $args, $context, $resolveInfo);
        $data = $data->getArrayCopy();

        if ($data['data']) {
            $data['data'] = base64_encode($data['data']);
        }

        return $data;
    }

    /**
     * @param null $value
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     * @throws \Exception
     */
    public function resolveCrop($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        return !empty($value['crop']) ? $value['crop'] : null;
    }

    /**
     * @param null $value
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     * @throws \Exception
     */
    public function resolveHotspots($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        return !empty($value['hotspots']) ? $value['hotspots'] : null;
    }

    /**
     * @param null $value
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     * @return array
     * @throws \Exception
     */
    public function resolveMarker($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        return !empty($value['marker']) ? $value['marker'] : null;
    }
}
