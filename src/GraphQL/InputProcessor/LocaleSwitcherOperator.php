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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\InputProcessor;


use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Model\DataObject\Concrete;

class LocaleSwitcherOperator extends BaseOperator
{


    protected $locale;
    /**
     * Base constructor.
     * @param $nodeDef
     */
    public function __construct($nodeDef)
    {
        parent::__construct($nodeDef);
        $this->locale = $nodeDef["attributes"]["locale"];

    }


    /**
     * @param Concrete $object
     * @param $newValue
     * @param $args
     * @param $context
     * @param ResolveInfo $info
     */
    public function process(Concrete $object, $newValue, $args, $context, ResolveInfo $info)
    {
        $localeService = $this->getGraphQlService()->getLocaleService();

        $currentLocale = $localeService->getLocale();

        $localeService->setLocale($this->locale);

        $class = $object->getClass();
        $parentProcessor = $this->getParentProcessor($this->nodeDef, $class);
        if ($parentProcessor) {
           call_user_func_array($parentProcessor, [$object, $newValue, $args, $context, $info]);
        }

        $localeService->setLocale($currentLocale);
    }
}

