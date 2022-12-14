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
use Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData;

class LocaleCollectorOperator extends BaseOperator
{
    /**
     * @param Concrete|AbstractData $object
     * @param array $newValue
     * @param array $args
     * @param array $context
     * @param ResolveInfo $info
     */
    public function process($object, $newValue, $args, $context, ResolveInfo $info)
    {
        $localeService = $this->getGraphQlService()->getLocaleService();
        $currentLocale = $localeService->getLocale();

        foreach ($newValue as $locale => $value) {
            $localeService->setLocale($locale);

            $class = $object->getClass();
            $parentProcessor = $this->getParentProcessor($this->nodeDef, $class);

            if ($parentProcessor) {
                call_user_func_array($parentProcessor, [$object, $value, $args, $context, $info]);
            }
        }

        $localeService->setLocale($currentLocale);
    }
}
