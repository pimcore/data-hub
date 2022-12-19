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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectInputProcessor;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData;

class Fieldcollections extends Base
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
     * @param array $newValue
     * @param array $args
     * @param array $context
     * @param ResolveInfo $info
     *
     * @throws \Exception
     */
    public function process($object, $newValue, $args, $context, ResolveInfo $info)
    {
        $attribute = $this->getAttribute();
        $getter = 'get' . ucfirst($attribute);
        $setter = 'set' . ucfirst($attribute);
        $currentCollection = $object->$getter();
        if ($currentCollection instanceof Fieldcollection) {
            $currentItems = $currentCollection->getItems() ?: [];
        } else {
            $currentItems = [];
        }

        // auto increment on group level!
        $autoIdx = 0;
        $newItems = [];

        if (! ($newValue['replace'] ?? false)) {
            foreach ($currentItems as $currentItem) {
                $newItems[$currentItem->getIndex()] = $currentItem;
            }
        }

        $itemGroups = $newValue['items'];
        foreach ($itemGroups as $fcKey => $groupItems) {
            $typeProcessor = $this->processors[$fcKey] ?? [];
            foreach ($groupItems as $groupItemData) {
                if (array_key_exists('index', $groupItemData)) {
                    $index = $groupItemData['index'];
                } else {
                    $index = $autoIdx;
                    $autoIdx++;
                }

                $fc = $newItems[$index] ?? null;

                if (!$fc || $fc->getType() != $fcKey) {
                    // either index does not exist or type does not match
                    $modelFactory = $this->getGraphQlService()->getModelFactory();
                    $className = 'Pimcore\\Model\\DataObject\\Fieldcollection\\Data\\' . ucfirst($fcKey);
                    /** @var AbstractData $fc */
                    $fc = $modelFactory->build($className);
                }
                $fc->setIndex($index);

                foreach ($groupItemData as $key => $value) {
                    if (isset($typeProcessor[$key])) {
                        $processor = $typeProcessor[$key];
                        call_user_func_array($processor, [$fc, $value, $args, $context, $info]);
                    }
                }
                $newItems[$index] = $fc;
            }
        }

        ksort($newItems);
        $newCollection = new Fieldcollection();
        $newCollection->setItems($newItems);
        $object->$setter($newCollection);
    }
}
