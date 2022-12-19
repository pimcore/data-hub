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

namespace Pimcore\Bundle\DataHubBundle\Service;

use GraphQL\Error\InvariantViolation;
use GraphQL\Server\RequestError;
use Symfony\Component\HttpFoundation\Request;

class FileUploadService
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     *
     * @throws RequestError
     */
    public function parseUploadedFiles(Request $request): array
    {
        $this->validateParsedBody($request);

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

                $items = $request->files->get($fileKey);
            }
        }

        return $result;
    }

    /**
     * Validates that the request meet our expectations
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     */
    protected function validateParsedBody(Request $request): void
    {
        $bodyParams = $request->request->all();

        if (empty($bodyParams)) {
            throw new InvariantViolation(
                'Request is expected to provide parsed body for "multipart/form-data" requests but got empty array'
            );
        }
    }
}
