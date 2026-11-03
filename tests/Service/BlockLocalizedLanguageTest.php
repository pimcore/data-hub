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

namespace Pimcore\Bundle\DataHubBundle\Tests\Service;

use Codeception\Test\Unit;
use Pimcore\Bundle\DataHubBundle\GraphQL\BlockDescriptor;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Cache\RuntimeCache;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Data\BlockElement;
use Pimcore\Model\DataObject\Localizedfield;
use Pimcore\Model\Element\Service as ElementService;

/**
 * A block row is read in the language requested for the block, also for the children that resolve their value
 * themselves (Date, relations): they hand their own field definition to Service::resolveValue(), not the block's.
 */
class BlockLocalizedLanguageTest extends Unit
{
    private const OBJECT_ID = 4301;

    private const DEFAULT_DATE = '2024-02-15';

    private string $cacheKey;

    private bool $wasRegistered = false;

    private mixed $previousObject = null;

    private bool $fallbackBefore;

    protected function setUp(): void
    {
        $this->cacheKey = ElementService::getElementCacheTag('object', self::OBJECT_ID);
        $this->wasRegistered = RuntimeCache::isRegistered($this->cacheKey);
        $this->previousObject = $this->wasRegistered ? RuntimeCache::get($this->cacheKey) : null;
        $this->fallbackBefore = Localizedfield::getGetFallbackValues();
    }

    protected function tearDown(): void
    {
        Localizedfield::setGetFallbackValues($this->fallbackBefore);
        if ($this->wasRegistered) {
            RuntimeCache::set($this->cacheKey, $this->previousObject);
        } elseif (RuntimeCache::isRegistered($this->cacheKey)) {
            RuntimeCache::getInstance()->offsetUnset($this->cacheKey);
        }
    }

    public function testDateChildOfLocalizedBlockUsesRequestedLanguage(): void
    {
        $this->storeObject(true);

        $date = $this->resolve('testDate', new Data\Date(), 'de');

        $this->assertSame('2025-01-22', $date);
    }

    public function testChildWithRelationDefinitionOfLocalizedBlockUsesRequestedLanguage(): void
    {
        $this->storeObject(true);

        $document = $this->resolve('document', new Data\ManyToOneRelation(), 'de');

        $this->assertSame('report-de', $document);
    }

    public function testChildOfLocalizedBlockWithoutLanguageUsesDefaultLanguage(): void
    {
        $this->storeObject(true);

        $this->assertSame(self::DEFAULT_DATE, $this->resolve('testDate', new Data\Date(), null));
    }

    public function testBlockOutsideLocalizedFieldsIsReadWithoutLanguage(): void
    {
        $this->storeObject(false);

        $this->assertSame(self::DEFAULT_DATE, $this->resolve('testDate', new Data\Date(), 'de'));
    }

    public function testChildOfLocalizedBlockPassesGetFallbackLanguageValueToTheGetter(): void
    {
        $object = $this->storeObject(true);

        Localizedfield::setGetFallbackValues(false);
        $this->resolve('testDate', new Data\Date(), 'de', true);
        $this->resolve('testDate', new Data\Date(), 'de', false);
        $this->resolve('testDate', new Data\Date(), 'de');

        $this->assertSame([true, false, false], $object->fallbackSeen);
    }

    public function testFallbackSettingIsRestoredAfterTheGetterFails(): void
    {
        $object = $this->storeObject(true);
        $object->failing = true;
        Localizedfield::setGetFallbackValues(false);

        try {
            $this->resolve('testDate', new Data\Date(), 'de', true);
            $this->fail('the getter should have thrown');
        } catch (\LogicException) {
            $this->assertFalse(Localizedfield::getGetFallbackValues());
        }
    }

    private function resolve(string $child, Data $definition, ?string $language, ?bool $fallback = null): mixed
    {
        $definition->setName($child);

        $descriptor = new BlockDescriptor();
        $descriptor['id'] = self::OBJECT_ID;
        $descriptor['__blockName'] = 'labTests';
        $descriptor['__blockIndex'] = 0;
        $descriptor['__blockFieldName'] = $child;
        $descriptor['args'] = $language === null ? [] : ['language' => $language];
        if ($fallback !== null) {
            $descriptor['args']['getFallbackLanguageValue'] = $fallback;
        }

        return Service::resolveValue($descriptor, $definition, $child);
    }

    /**
     * Stores an object whose getLabTests() returns one row per language; a block outside localized fields has a
     * getter without a language and always returns the default row.
     */
    private function storeObject(bool $localized): Concrete
    {
        $rows = [
            'en' => ['testDate' => self::DEFAULT_DATE, 'document' => 'report-en'],
            'de' => ['testDate' => '2025-01-22', 'document' => 'report-de'],
        ];

        $block = new Data\Block();
        $block->setName('labTests');
        $class = new ClassDefinition();
        if ($localized) {
            $localizedFields = new Data\Localizedfields();
            $localizedFields->setName('localizedfields');
            $localizedFields->setChildren([$block]);
            $class->addFieldDefinition('localizedfields', $localizedFields);
        } else {
            $class->addFieldDefinition('labTests', $block);
        }

        $object = new class() extends Concrete {
            /** @var array<string, array<string, string>> */
            public array $rows = [];

            /** @var list<bool> */
            public array $fallbackSeen = [];

            public bool $failing = false;

            public function getLabTests(?string $language = null): array
            {
                $this->fallbackSeen[] = Localizedfield::getGetFallbackValues();
                if ($this->failing) {
                    throw new \LogicException('getter failed');
                }

                $row = $this->rows[$language ?? 'en'] ?? [];
                $elements = array_combine(
                    array_keys($row),
                    array_map(
                        static fn (string $name, string $value): BlockElement => new BlockElement($name, 'input', $value),
                        array_keys($row),
                        $row
                    )
                );

                return [$elements];
            }
        };
        $object->rows = $rows;
        $object->setId(self::OBJECT_ID);
        $object->setClass($class);

        RuntimeCache::set($this->cacheKey, $object);

        return $object;
    }
}
