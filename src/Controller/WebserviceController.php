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

use GraphQL\Error\DebugFlag;
use GraphQL\Error\Warning;
use GraphQL\GraphQL;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\ExecutorEvents;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\Model\ExecutorEvent;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\Model\ExecutorResultEvent;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassTypeDefinitions;
use Pimcore\Bundle\DataHubBundle\GraphQL\Mutation\MutationType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Query\QueryType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\PimcoreDataHubBundle;
use Pimcore\Bundle\DataHubBundle\Service\CheckConsumerPermissionsService;
use Pimcore\Bundle\DataHubBundle\Service\FileUploadService;
use Pimcore\Bundle\DataHubBundle\Service\OutputCacheService;
use Pimcore\Cache\Runtime;
use Pimcore\Controller\FrontendController;
use Pimcore\Localization\LocaleServiceInterface;
use Pimcore\Logger;
use Pimcore\Model\Factory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class WebserviceController extends FrontendController
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var CheckConsumerPermissionsService
     */
    private $permissionsService;

    /**
     * @var OutputCacheService
     */
    private $cacheService;

    /**
     * @var FileUploadService
     */
    private $uploadService;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        CheckConsumerPermissionsService $permissionsService,
        OutputCacheService $cacheService,
        FileUploadService $uploadService
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->permissionsService = $permissionsService;
        $this->cacheService = $cacheService;
        $this->uploadService = $uploadService;
    }

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
    public function webonyxAction(
        Service $service,
        LocaleServiceInterface $localeService,
        Factory $modelFactory,
        Request $request
    ) {
        $clientname = $request->get('clientname');

        $configuration = Configuration::getByName($clientname);
        if (!$configuration || !$configuration->isActive()) {
            throw new NotFoundHttpException('No active configuration found for ' . $clientname);
        }

        if (!$this->permissionsService->performSecurityCheck($request, $configuration)) {
            throw new AccessDeniedHttpException('Permission denied, apikey not valid');
        }

        if ($response = $this->cacheService->load($request)) {
            Logger::debug('Loading response from cache');

            return $response;
        }

        Logger::debug('Cache entry not found');

        // context info, will be passed on to all resolver function
        $context = ['clientname' => $clientname, 'configuration' => $configuration];

        $config = $this->getParameter('pimcore_data_hub');

        if (isset($config['graphql']) && isset($config['graphql']['not_allowed_policy'])) {
            PimcoreDataHubBundle::setNotAllowedPolicy($config['graphql']['not_allowed_policy']);
        }
        Runtime::set('datahub_context', $context);

        ClassTypeDefinitions::build($service, $context);

        $queryType = new QueryType($service, $localeService, $modelFactory, $this->eventDispatcher, [], $context);
        $mutationType = new MutationType($service, $localeService, $modelFactory, $this->eventDispatcher, [], $context);

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

        $contentType = $request->headers->get('content-type') ?? '';

        if (mb_stripos($contentType, 'multipart/form-data') !== false) {
            $input = $this->uploadService->parseUploadedFiles($request);
        } else {
            $input = json_decode($request->getContent(), true);
        }

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

            $event = new ExecutorEvent(
                $request,
                $query,
                $schema,
                $context
            );

            $this->eventDispatcher->dispatch($event, ExecutorEvents::PRE_EXECUTE);

            if ($event->getRequest() instanceof Request) {
                $variableValues = $event->getRequest()->get('variables', $variableValues);
            }

            $result = GraphQL::executeQuery(
                $event->getSchema(),
                $event->getQuery(),
                $rootValue,
                $event->getContext(),
                $variableValues,
                null,
                null,
                $validators

            );

            $exResult = new ExecutorResultEvent($request, $result);
            $this->eventDispatcher->dispatch($exResult, ExecutorEvents::POST_EXECUTE);
            $result = $exResult->getResult();

            if (\Pimcore::inDebugMode()) {
                $debug = DebugFlag::INCLUDE_DEBUG_MESSAGE | DebugFlag::INCLUDE_TRACE | DebugFlag::RETHROW_INTERNAL_EXCEPTIONS;
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

        $origin = '*';
        if (!empty($_SERVER['HTTP_ORIGIN'])) {
            $origin = $_SERVER['HTTP_ORIGIN'];
        }

        $response = new JsonResponse($output);
        $response->headers->set('Access-Control-Allow-Origin', $origin);
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Origin, Content-Type, X-Auth-Token');

        $this->cacheService->save($request, $response);

        return $response;
    }
}
