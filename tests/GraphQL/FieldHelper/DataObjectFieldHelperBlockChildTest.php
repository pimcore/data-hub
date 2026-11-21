<?php

declare(strict_types=1);

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\DataHubBundle\Tests\GraphQL\FieldHelper;

use Codeception\Attribute\DataProvider;
use Codeception\Test\Unit;
use Pimcore;
use Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper\DataObjectFieldHelper;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Psr\Log\AbstractLogger;
use ReflectionProperty;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\KernelInterface;

class DataObjectFieldHelperBlockChildTest extends Unit
{
    private AbstractLogger $logger;

    private ?KernelInterface $previousKernel = null;

    protected function _before(): void
    {
        $this->logger = new class() extends AbstractLogger {
            /** @var string[] */
            public array $messages = [];

            public function log($level, string|\Stringable $message, array $context = []): void
            {
                $this->messages[] = $level . ': ' . $message;
            }
        };

        $this->previousKernel = Pimcore::getKernel();

        $container = new Container();
        $container->set('monolog.logger.pimcore', $this->logger);
        $kernel = $this->createMock(KernelInterface::class);
        $kernel->method('getContainer')->willReturn($container);
        Pimcore::setKernel($kernel);
    }

    protected function _after(): void
    {
        (new ReflectionProperty(Pimcore::class, 'kernel'))->setValue(null, $this->previousKernel);
    }

    /**
     * Regression test for https://github.com/pimcore/data-hub/issues/1027:
     * a query column naming a field inside a block (also block -> localizedfields) is served by the
     * block entry type, so skipping it in the column list must not log an error (only a debug note).
     */
    #[DataProvider('blockChildKeys')]
    public function testBlockChildColumnIsSkippedWithoutErrorLog(string $key): void
    {
        $helper = new DataObjectFieldHelper();

        $query = $helper->getQueryFieldConfigFromConfig($this->columnConfig($key), $this->createClass());

        $this->assertFalse($query);
        $this->assertCount(1, $this->logger->messages);
        $this->assertStringStartsWith('debug: skipping column "' . $key . '"', $this->logger->messages[0]);
        $this->assertStringContainsString('block "officeHours"', $this->logger->messages[0]);
    }

    public static function blockChildKeys(): array
    {
        return [
            'block child' => ['plain'],
            'block localized child' => ['officeHoursTimesInput'],
        ];
    }

    public function testUnknownColumnStillLogsError(): void
    {
        $helper = new DataObjectFieldHelper();

        $nodeDef = $this->columnConfig('doesNotExist');
        $this->assertFalse($helper->getQueryFieldConfigFromConfig($nodeDef, $this->createClass()));
        $this->assertFalse($helper->getMutationFieldConfigFromConfig($nodeDef, $this->createClass()));

        $this->assertSame([
            'error: could not resolve field "doesNotExist" in class Salespartner',
            'error: could not resolve field doesNotExist',
        ], $this->logger->messages);
    }

    private function columnConfig(string $key): array
    {
        return ['isOperator' => false, 'attributes' => ['attribute' => $key, 'dataType' => 'input']];
    }

    private function createClass(): ClassDefinition
    {
        $plain = new Data\Input();
        $plain->setName('plain');

        $timesInput = new Data\Input();
        $timesInput->setName('officeHoursTimesInput');

        $localized = new Data\Localizedfields();
        $localized->setName('localizedfields');
        $localized->setChildren([$timesInput]);

        $block = new Data\Block();
        $block->setName('officeHours');
        $block->setChildren([$plain, $localized]);

        $title = new Data\Input();
        $title->setName('title');

        $class = new ClassDefinition();
        $class->setName('Salespartner');
        $class->setFieldDefinitions(['title' => $title, 'officeHours' => $block]);

        return $class;
    }
}
