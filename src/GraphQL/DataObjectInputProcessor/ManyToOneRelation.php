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
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ElementIdentificationTrait;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData;
use Pimcore\Model\Exception\NotFoundException;

/**
 * @internal
 */
final class ManyToOneRelation extends Base
{
    use ElementIdentificationTrait;

    /**
     * @param Concrete|AbstractData $object
     * @param array $newValue
     * @param array $args
     * @param array $context
     *
     * @throws \Exception
     */
    public function process($object, $newValue, $args, $context, ResolveInfo $info): void
    {
        $attribute = $this->getAttribute();
        $me = $this;
        Service::setValue($object, $attribute, function ($container, $setter) use ($newValue) {
            $element = null;

            if (is_array($newValue)) {
                $element = $this->getElementByTypeAndIdOrPath($newValue);

                if (!$element) {
                    throw new NotFoundException(
                        sprintf('Element with id %s or fullpath %s not found',
                            $newValue['id'],
                            $newValue['fullpath']
                        )
                    );
                }
            }

            return $container->$setter($element);
        });
    }
}
