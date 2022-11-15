<?php
namespace Pimcore\Bundle\PimcoreDataHubBundle\Tests\Helper;

use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Unittest;
use Pimcore\Tests\Support\Helper\ClassManager;
use Pimcore\Tests\Support\Helper\DataType\TestDataHelper;
use Pimcore\Tests\Support\Helper\Model;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class Service extends Model
{

    /**
     * @var null|ContainerBagInterface
     */
    protected static $container = null;

    public function grabService(string $serviceId) {

        //TODO change this as soon as Pimcore helper as grabService method and requirement is bumped to pimcore/pimcore:10.4
        if(empty(self::$container)) {
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
