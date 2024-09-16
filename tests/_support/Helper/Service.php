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

namespace Pimcore\Bundle\PimcoreDataHubBundle\Tests\Helper;

use Pimcore\Model\DataObject\Unittest;
use Pimcore\Tests\Support\Helper\Model;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class Service extends Model
{
    /**
     * @var null|ContainerBagInterface
     */
    protected static $container = null;

    /**
     * @return object|null
     */
    public function grabService(string $serviceId)
    {

        //TODO change this as soon as Pimcore helper as grabService method and requirement is bumped to pimcore/pimcore:10.4
        if (empty(self::$container)) {
            $container = \Pimcore::getContainer();
            self::$container = $container->has('test.service_container') ? $container->get('test.service_container') : $container;
        }

        return self::$container->get($serviceId);
    }

    public function initializeDefinitions()
    {
        //        $this->setupFieldcollection_Unittestfieldcollection();
        //        $this->setupPimcoreClass_Unittest();
        //        $this->setupObjectbrick_UnittestBrick();
    }
}
