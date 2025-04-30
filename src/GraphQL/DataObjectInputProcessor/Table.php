<?php

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectInputProcessor;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData;

/**
 * @internal
 */
final class Table extends Base
{
    /**
     * @var array
     */
    protected $processors;

    public function __construct(array $nodeDef, array $processors)
    {
        parent::__construct($nodeDef);
        $this->processors = $processors;
    }

    /**
     * @param Concrete|AbstractData $object
     * @param mixed $newValue
     * @param array $args
     * @param array $context
     *
     * @throws \Exception
     */
    public function process($object, $newValue, $args, $context, ResolveInfo $info)
    {
        $attribute = $this->getAttribute();
        $objectBrickParts = Service::parseObjectBrickFieldName($attribute);

        if (empty($objectBrickParts)) {
            $getter = 'get' . ucfirst($attribute);
            $currentTable = $object->$getter();
        } else {
            $currentTable = Service::getValueFromObjectBrick(
                $object,
                $objectBrickParts['brickType'],
                $objectBrickParts['brickKey'],
                $objectBrickParts['brickDescriptor'] ?? null
            );
        }

        Service::setValue($object, $attribute, function ($container, $setter) use ($newValue, $currentTable) {
            $newTable = [];

            if ($newValue === null) {
                return $container->$setter($currentTable);
            }

            if (! ($newValue['replace'] ?? false)) {
                if (count($currentTable) > 0) {
                    foreach ($currentTable as $row) {
                        $newTable[] = $row;
                    }
                } elseif ($tableHeader = $this->processors['tableHeader']) {
                    $newTable[] = $tableHeader;
                }
            } elseif ($tableHeader = $this->processors['tableHeader']) {
                $newTable[] = $tableHeader;
            }

            if (is_array($newValue['rows'])) {
                foreach ($newValue['rows'] as $row) {
                    $values = array_values($row);
                    $newTable[] = $values;
                }

                return $container->$setter($newTable);
            }

            return null;
        });
    }
}
