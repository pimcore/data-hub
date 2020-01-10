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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\Document;
use Pimcore\Model\Property;


class Element
{

    use ServiceTrait;

    /** @var string */
    protected $elementType;

    public function __construct(string $elementType)
    {
        $this->elementType = $elementType;
    }

    /**
     * @param null $value
     * @param array $args
     * @param $context
     * @param ResolveInfo|null $resolveInfo
     */
    public function resolveProperties($value = null, $args = [], $context, ResolveInfo $resolveInfo = null)
    {
        $elementId = $value["id"];
        $element = null;

        switch ($this->elementType) {
            case 'asset':
                $element = Asset::getById($elementId);
                break;
            case 'document':
                $element = Document::getById($elementId);
                break;
            case 'object':
                $element = AbstractObject::getById($elementId);
                break;
            default:
                trigger_error("unknown element type");
        }

        if (!$element) {
            throw new \Exception("element " . $this->elementType . " " . $elementId . " not found");
        }

        if (isset($args['keys'])) {
            $result = [];
            $properties = $element->getProperties();
            /** @var $property Property */
            foreach ($properties as $property) {
                if (in_array($property->getName(), $args['keys'])) {
                    $result[] = $property;
                }
            }
        } else {
            $result = $element->getProperties();
        }

        return $result;
    }

}

