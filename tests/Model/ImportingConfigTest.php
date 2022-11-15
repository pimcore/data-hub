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
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace Pimcore\Tests\Model\DataObject;

use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Tests\Support\Helper\DataType\TestDataHelper;
use Pimcore\Tests\Support\Test\ModelTestCase;
use Pimcore\Model\DataObject\DataHubTestEntity;

/**
 * Class ListingTest
 *
 * @package Pimcore\Tests\Model\DataObject
 * @group model.dataobject.listing
 */
class ImportingConfigTest extends ModelTestCase
{
    /**
     * @var TestDataHelper
     */
    protected $testDataHelper;

    const CORRECT_API_KEY = "correct_key";
    const CONFNAME = 'newone';

    public function setUp(): void
    {
        parent::setUp();
        //TestHelper::cleanUp();
        //$this->prepareData();
    }

    public function tearDown(): void
    {
        //TestHelper::cleanUp();
//        parent::tearDown();
    }



    public function testConfiguration()
    {

        $config = Configuration::getByName(self::CONFNAME);
        $this->assertEquals(false, $config instanceof Configuration, 'Check if configuration exists ' . self::CONFNAME);

        $config = new Configuration('graphql', '/124', self::CONFNAME);


        $configurationData = file_get_contents(__DIR__ . '/../_support/Resources/configuration_query_mutation_allowed.json');
        $decodedConfigurationData = json_decode($configurationData, true);
        $config->setConfiguration($decodedConfigurationData);

        $config->save();
        $config = Configuration::getByName(self::CONFNAME);

        //this works locally but not on github actions
        $this->assertEquals(true, $config instanceof Configuration, 'Check if configuration is successfully saved ' . self::CONFNAME . ': ' . print_r($config, true));
    }
}
