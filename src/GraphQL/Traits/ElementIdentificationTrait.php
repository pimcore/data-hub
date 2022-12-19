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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Traits;

use Pimcore\Bundle\DataHubBundle\GraphQL\Exception\ClientSafeException;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Model\Element\Service;

trait ElementIdentificationTrait
{
    private $typeKey = 'type';
    private $idKey = 'id';
    private $fullpathKey = 'fullpath';
    private $supportedTypes = ['object', 'asset', 'document'];

    /**
     * Returns an element (object, document or asset) identified whether by id or fullpath
     *
     * @param array $value array including type (if not passed in the optional type argument), as well as id or fullpath (in case both are provided the id will be priorized)
     * @param string|null $type can whether be 'object', 'asset' or 'document'
     *
     * @return ElementInterface|null can whether be an object, a document or an asset
     *
     * @throws ClientSafeException thrown if no type or neither an id nor a fullpath is provided
     */
    public function getElementByTypeAndIdOrPath($value, $type = null)
    {
        if (!isset($type)) {
            $type = $this->getType($value);
        }

        if (!in_array($type, $this->supportedTypes)) {
            throw new ClientSafeException('The type "' . $type . '" is not supported');
        }

        $isIdSet = $value[$this->idKey] ?? false;
        $isFullpathSet = $value[$this->fullpathKey] ?? false;

        if ($isIdSet && $isFullpathSet) {
            throw new ClientSafeException('either id or fullpath expected but not both');
        }

        if ($isIdSet) {
            return $this->getElementById($type, $value[$this->idKey]);
        }

        if ($isFullpathSet) {
            return $this->getElementByPath($type, $value[$this->fullpathKey]);
        }

        throw new ClientSafeException('either ' . $this->idKey . ' or ' . $this->fullpathKey . ' expected');
    }

    /**
     * Can be overwritten
     */
    protected function getElementById($type, $id)
    {
        return Service::getElementById($type, $id);
    }

    /**
     * Can be overwritten
     */
    protected function getElementByPath($type, $fullpath)
    {
        return Service::getElementByPath($type, $fullpath);
    }

    /**
     * @param array $value
     *
     * @return mixed
     *
     * @throws ClientSafeException
     */
    private function getType($value)
    {
        if (!isset($value[$this->typeKey])) {
            throw new ClientSafeException('type expected');
        }

        return $value[$this->typeKey];
    }
}
