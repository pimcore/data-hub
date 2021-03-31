<?php

namespace Pimcore\Bundle\DataHubBundle\EventListener;

use GraphQL\Error\InvariantViolation;
use GraphQL\Server\RequestError;
use GraphQL\Utils\Utils;
use Pimcore\Bundle\DataHubBundle\Controller\WebserviceController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class KernelControllerListener implements EventSubscriberInterface
{
    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\ControllerEvent $event
     *
     * @throws \GraphQL\Server\RequestError
     */
    public function onKernelController(ControllerEvent $event): void
    {
        $controller = $event->getController();

        // when a controller class defines multiple action methods, the controller
        // is returned as [$controllerInstance, 'methodName']
        if (is_array($controller)) {
            $controller = $controller[0];
        }

        if ($controller instanceof WebserviceController) {
            $request = $event->getRequest();
            $contentType = $request->getContentType() ?? '';

            if (mb_stripos($contentType, 'multipart/form-data') !== false) {
                $this->validateParsedBody($request);

                $this->parseUploadedFiles($request);
            }
        }
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \GraphQL\Server\RequestError
     */
    protected function parseUploadedFiles(Request $request): void
    {
        $bodyParams = $request->request->all();

        if (!isset($bodyParams['map'])) {
            throw new RequestError('The request must define a `map`');
        }

        $map = json_decode($bodyParams['map'], true);
        $result = json_decode($bodyParams['operations'], true);

        foreach ($map as $fileKey => $locations) {
            foreach ($locations as $location) {
                $items = &$result;

                foreach (explode('.', $location) as $key) {
                    if (!isset($items[$key]) || !is_array($items[$key])) {
                        $items[$key] = [];
                    }

                    $items = &$items[$key];
                }

                $items = $request->files[$fileKey];
            }
        }
    }

    /**
     * Validates that the request meet our expectations
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \GraphQL\Server\RequestError
     */
    protected function validateParsedBody(Request $request): void
    {
        $bodyParams = $request->request->all();

        if (null === $bodyParams) {
            throw new InvariantViolation(
                'Request is expected to provide parsed body for "multipart/form-data" requests but got null'
            );
        }

        if (!is_array($bodyParams)) {
            throw new RequestError(
                'GraphQL Server expects JSON object or array, but got ' . Utils::printSafeJson($bodyParams)
            );
        }

        if (empty($bodyParams)) {
            throw new InvariantViolation(
                'Request is expected to provide parsed body for "multipart/form-data" requests but got empty array'
            );
        }
    }
}
