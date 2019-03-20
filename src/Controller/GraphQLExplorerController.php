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

namespace Pimcore\Bundle\DataHubBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class GraphQLExplorerController extends Controller
{
    /**
     * @Cache(expires="tomorrow", public=true)
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function explorerAction(Request $request)
    {
        $allParams = array_merge($request->request->all(), $request->query->all());

        $clientname = $request->get('clientname');

        $route = \Pimcore::getContainer()->get('router')->getRouteCollection()->get('admin_pimcoredatahub_webservice');
        if ($route) {
            $url = $route->getPath();
            $url = str_replace('/{clientname}', '', $url);
        } else {
            throw now \Exception('unable to resolve');
        }

        if ($clientname) {
            $url .= '/' . $clientname;
        }

        $urlParams = $allParams;

        if ($urlParams) {
            $url = $url . '?' . http_build_query($urlParams);
        }

        return $this->render('PimcoreDataHubBundle:Feature:explorer.html.twig', [
            'graphQLUrl' => $url,
            'tokenHeader' => 'access-token'
        ]);
    }
}
