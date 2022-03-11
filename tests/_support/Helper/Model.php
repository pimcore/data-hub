<?php

namespace DataHubBundle\Tests\Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Pimcore\Bundle\DataHubBundle\Installer;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Customer;
use Pimcore\Tests\Helper\AbstractDefinitionHelper;
use Pimcore\Tests\Helper\Pimcore;
use Pimcore\Tests\Helper\Model as PimcoreModel;
use Pimcore\Tests\Util\Autoloader;

class Model extends AbstractDefinitionHelper
{

    public function _beforeSuite($settings = [])
    {
        /** @var Pimcore $pimcoreModule */
        $pimcoreModule = $this->getModule('\\' . Pimcore::class);

        $this->debug('[DataHub] Running datahub installer');


        //create migrations table in order to allow installation - needed for SettingsStoreAware Installer
        \Pimcore\Db::get()->exec('
create table migration_versions
(
	version varchar(1024) not null
		primary key,
	executed_at datetime null,
	execution_time int null
)
collate=utf8_unicode_ci;

');

        // install datahub bundle
        $installer = $pimcoreModule->getContainer()->get(Installer::class);
        $installer->install();

        $this->initializeDefinitions();
        Autoloader::load(DataHubTestEntity::class);
    }

    public function initializeDefinitions()
    {
        $cm = $this->getClassManager();
        $cm->setupClass('DataHubTestEntity', __DIR__ . '/../Resources/class_DataHubTestEntity_import.json');
    }
}
