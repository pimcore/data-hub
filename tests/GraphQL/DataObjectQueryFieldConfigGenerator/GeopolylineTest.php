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

namespace Pimcore\Bundle\DataHubBundle\Tests\GraphQL\DataObjectQueryFieldConfigGenerator;

use Codeception\Test\Unit;
use GraphQL\Type\Definition\ListOfType;
use Pimcore\Bundle\DataHubBundle\DependencyInjection\Compiler\ImportExportLocatorsPass;
use Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectQueryFieldConfigGenerator\Geopolygon;
use Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType\GeopointType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use ReflectionClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Regression test for registering the existing Geopolygon generator under a second tag ID for
 * geopolyline (see graphql.yml) instead of duplicating it - a geopolyline is stored identically
 * to a geopolygon (an ordered array of GeoCoordinates).
 *
 * `bin/console debug:container` alone only confirms the container compiles; it would not catch a
 * typo'd tag `id:` (the resulting locator would simply be missing that key, only surfacing as a
 * runtime "not found" error the first time a geopolyline field is actually queried) or the
 * generator resolving to the wrong GraphQL type. This exercises the real compiler pass -
 * ImportExportLocatorsPass, which builds the locator from tagged services - and asserts both the
 * tag resolution and the produced field type directly.
 */
class GeopolylineTest extends Unit
{
    private const TAG = 'pimcore.datahub.graphql.dataobjectquerytypegenerator';

    private const GEOPOLYGON_TAG_ID = 'typegenerator_dataobjectquerydatatype_geopolygon';

    private const GEOPOLYLINE_TAG_ID = 'typegenerator_dataobjectquerydatatype_geopolyline';

    public function testGeopolylineTagResolvesToGeopolygonGenerator(): void
    {
        $container = new ContainerBuilder();
        $container->register(Service::class)->setPublic(true);
        $container->register(Geopolygon::class)
            ->addTag(self::TAG, ['id' => self::GEOPOLYGON_TAG_ID])
            ->addTag(self::TAG, ['id' => self::GEOPOLYLINE_TAG_ID]);

        (new ImportExportLocatorsPass())->process($container);

        $serviceDefinition = $container->getDefinition(Service::class);
        $locatorDefinition = $serviceDefinition->getArgument('$dataObjectQueryTypeGeneratorFactories');
        $mapping = $locatorDefinition->getArgument(0);

        $this->assertArrayHasKey(self::GEOPOLYLINE_TAG_ID, $mapping);

        $reference = $mapping[self::GEOPOLYLINE_TAG_ID];
        $this->assertInstanceOf(Reference::class, $reference);
        $this->assertSame(Geopolygon::class, (string) $reference);
    }

    public function testGeopolygonGeneratorProducesAListOfGeopointType(): void
    {
        $generator = (new ReflectionClass(Geopolygon::class))->newInstanceWithoutConstructor();

        $fieldType = $generator->getFieldType($this->createStub(Data::class));

        $this->assertInstanceOf(ListOfType::class, $fieldType);
        $this->assertSame(GeopointType::getInstance(), $fieldType->getWrappedType());
    }
}
