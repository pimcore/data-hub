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

namespace Pimcore\Bundle\DataHubBundle\Controller;

use Pimcore\Bundle\DataHubBundle\Service\CheckConsumerPermissionsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class GraphQLExplorerController extends AbstractController
{
    /**
     * @param RouterInterface $routingService
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function explorerAction(RouterInterface $routingService, Request $request)
    {
        $urlParams = array_merge($request->request->all(), $request->query->all());

        $clientName = $request->get('clientname');

        $url = $routingService->generate('admin_pimcoredatahub_webservice', ['clientname' => $clientName]);

        if (!$url) {
            throw new \Exception('unable to resolve');
        }

        if ($urlParams) {
            $url = $url . '?' . http_build_query($urlParams);
        }

        $response = $this->render('@PimcoreDataHub/Feature/explorer.html.twig', [
            'graphQLUrl' => $url,
            'tokenHeader' => CheckConsumerPermissionsService::TOKEN_HEADER
        ]);

        $response->setPublic();
        $response->setExpires(new \DateTime('tomorrow'));

        return $response;
    }
}
