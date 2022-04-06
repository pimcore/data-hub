<?php

use Pimcore\Tests\Util\Autoloader;

$pimcoreTestsSupportDir = '';

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    include __DIR__ . '/../vendor/autoload.php';
    $pimcoreTestsSupportDir = __DIR__ . '/../vendor/pimcore/pimcore/tests/_support';
} elseif (file_exists(__DIR__ . '/../../../../vendor/autoload.php')) {
    include __DIR__ . '/../../../../vendor/autoload.php';
    $pimcoreTestsSupportDir = __DIR__ . '/../../../../vendor/pimcore/pimcore/tests/_support';
} elseif (getenv('PIMCORE_PROJECT_ROOT') != '' && file_exists(getenv('PIMCORE_PROJECT_ROOT') . '/vendor/autoload.php')) {
    include getenv('PIMCORE_PROJECT_ROOT') . '/vendor/autoload.php';
    $pimcoreTestsSupportDir = getenv('PIMCORE_PROJECT_ROOT') . '/vendor/pimcore/pimcore/tests/_support';
} elseif (getenv('PIMCORE_PROJECT_ROOT') != '') {
    throw new \Exception('Invalid Pimcore project root "' . getenv('PIMCORE_PROJECT_ROOT') . '"');
} else {
    throw new \Exception('Unknown configuration! Pimcore project root not found, please set env variable PIMCORE_PROJECT_ROOT.');
}

include $pimcoreTestsSupportDir . '/Util/Autoloader.php';

\Pimcore\Bootstrap::setProjectRoot();
\Pimcore\Bootstrap::bootstrap();

//error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_WARNING);

//Codeception\Util\Autoload::addNamespace();
Autoloader::addNamespace('Pimcore\Tests', $pimcoreTestsSupportDir);
//Autoloader::addNamespace('Pimcore\Model\DataObject', __DIR__ . '/_output/var/classes/DataObject');
Autoloader::addNamespace('Pimcore\Model\DataObject', PIMCORE_CLASS_DIRECTORY . '/DataObject');
Autoloader::addNamespace('DataHubBundle\Tests', __DIR__);
Autoloader::addNamespace('DataHubBundle\Tests', __DIR__ . '/_support');


echo __DIR__ . '/_support';

if (!defined('TESTS_PATH')) {
    define('TESTS_PATH', __DIR__);
}

if (!defined('PIMCORE_TEST')) {
    define('PIMCORE_TEST', true);
}
