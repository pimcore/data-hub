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

namespace Pimcore\Bundle\DataHubBundle\Controller;

use GraphQL\Error\Debug;
use GraphQL\Error\Warning;
use GraphQL\GraphQL;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassTypeDefinitions;
use Pimcore\Bundle\DataHubBundle\GraphQL\Mutation\MutationType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Query\QueryType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\PimcoreDataHubBundle;
use Pimcore\Cache\Runtime;
use Pimcore\Controller\FrontendController;
use Pimcore\Localization\LocaleServiceInterface;
use Pimcore\Logger;
use Pimcore\Model\Factory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class WebserviceController extends FrontendController
{
    /**
     * @param Service $service
     * @param LocaleServiceInterface $localeService
     * @param Factory $modelFactory
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function webonyxAction(Service $service, LocaleServiceInterface $localeService, Factory $modelFactory, Request $request)
    {
        $clientname = $request->get('clientname');

        $configuration = Configuration::getByName($clientname);
        if (!$configuration) {
            throw new \Exception('No configuration found for ' . $clientname);
        }

        if (!$configuration->isActive()) {
            throw new \Exception('Configuration not active');
        }

        $this->performSecurityCheck($request, $configuration);

        // context info, will be passed on to all resolver function
        $context = ['clientname' => $clientname, 'configuration' => $configuration];

        $config = $this->container->getParameter('pimcore_data_hub');

        if (isset($config['graphql']) && isset($config['graphql']['not_allowed_policy'])) {
            PimcoreDataHubBundle::setNotAllowedPolicy($config['graphql']['not_allowed_policy']);
        }
        Runtime::set('datahub_context', $context);

        ClassTypeDefinitions::build($service, $context);

        $queryType = new QueryType($service, $localeService, $modelFactory, [], $context);
        $mutationType = new MutationType($service, $localeService, $modelFactory, [], $context);


        try {
            $schemaConfig = [
                'query' => $queryType
            ];
            if (!$mutationType->isEmpty()) {
                $schemaConfig['mutation'] = $mutationType;
            }
            $schema = new \GraphQL\Type\Schema(
                $schemaConfig
            );
        } catch (\Exception $e) {
            Warning::enable(false);
            $schema = new \GraphQL\Type\Schema(
                [
                    'query' => $queryType,
                    'mutation' => $mutationType
                ]
            );
            $schema->assertValid();
            Logger::error($e);
            throw $e;
        }

        $rawInput = file_get_contents('php://input');
        $input = json_decode($rawInput, true);
        $query = $input['query'];
        $variableValues = isset($input['variables']) ? $input['variables'] : null;

        try {
            $rootValue = [];

            $validators = null;
            if ($request->get('novalidate')) {
                // disable all validators except the listed ones
                $validators = [
//                    new NoUndefinedVariables()
                ];
            }

            $result = GraphQL::executeQuery(
                $schema,
                $query,
                $rootValue,
                $context,
                $variableValues,
                null,
                null,
                $validators

            );

            if (PIMCORE_DEBUG) {
                $debug = Debug::INCLUDE_DEBUG_MESSAGE | Debug::INCLUDE_TRACE | Debug::RETHROW_INTERNAL_EXCEPTIONS;
                $output = $result->toArray($debug);
            } else {
                $output = $result->toArray(false);
            }
        } catch (\Exception $e) {
            $output = [
                'errors' => [
                    [
                        'message' => $e->getMessage(),
                    ],
                ],
            ];
        }

        return new JsonResponse($output);
    }

    /**
     * @param Request $request
     * @param Configuration $configuration
     *
     * @return bool
     *
     * @throws \Exception
     */
    protected function performSecurityCheck(Request $request, Configuration $configuration)
    {
        $securityConfig= $configuration->getSecurityConfig();
        if ($securityConfig['method'] == 'datahub_apikey') {
            $apiKey = $request->get('apikey');
            if ($apiKey == $securityConfig['apikey']) {
                return true;
            }
        }
        throw new \Exception('permission denied');
    }
}
