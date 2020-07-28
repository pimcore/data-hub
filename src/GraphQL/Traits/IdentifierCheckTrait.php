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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Traits;

use Pimcore\Bundle\DataHubBundle\GraphQL\Exception\ClientSafeException;

trait IdentifierCheckTrait
{
    private $typeKey = 'type';
    private $idKey = 'id';
    private $fullpathKey = 'fullpath';

    /**
     * Returns an object of a specific entity identified whether by id or fullpath
     * @param string $entity object type which is expected and supported by the graphQL configuration
     * @param array $value array including id or fullpath (in case both are provided the id will be priorized)
     * @return ElementInterface|null can whether be an object
     * @throws \ClientSafeException thrown if neither an id nor a fullpath is provided
     */
    public function getObjectByEntityAndIdOrPath($entity, $value)
    {
        $className = 'Pimcore\\Model\\DataObject\\' . ucfirst($entity);

        if (isset($value[$this->idKey])) {
            return $className::getById($value[$this->idKey]);
        } else if (isset($value[$this->fullpathKey])) {
            return $className::getByPath($value[$this->fullpathKey]);
        }

        throw new ClientSafeException('Either ' . $this->idKey . ' or ' . $this->fullpathKey . ' expected');
    }

    /**
     * Returns an element (object, document or asset) identified whether by id or fullpath
     * @param array $value array including type, as well as id or fullpath (in case both are provided the id will be priorized)
     * @return ElementInterface|null can whether be an object, a document or an asset
     * @throws \ClientSafeException thrown if no type or neither an id nor a fullpath is provided
     */
    public function getElementByIdOrPath($value)
    {
        if (!isset($value[$this->typeKey])) {
            throw new ClientSafeException('type expected');
        }

        $type = $value[$this->typeKey];
        return $this->getElementByTypeAndIdOrPath($type, $value);
    }

    private function getElementByTypeAndIdOrPath($type, $value)
    {
        if (isset($value[$this->idKey])) {
            return $this->getElementById($type, $value[$this->idKey]);
        }

        if (isset($value[$this->fullpathKey])) {
            return $this->getElementByPath($type, $value[$this->fullpathKey]);
        }

        throw new ClientSafeException('Either ' . $this->idKey . ' or ' . $this->fullpathKey . ' expected');
    }

    /**
     * Can be overwritten if the source has been changed
     */
    protected function getElementById($type, $id)
    {
        return \Pimcore\Model\Element\Service::getElementById($type, $id);
    }

    /**
     * Can be overwritten if the source has been changed
     */
    protected function getElementByPath($type, $fullpath)
    {
        return \Pimcore\Model\Element\Service::getElementByPath($type, $fullpath);
    }
}
