<?php declare(strict_types=1);

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\DataHubBundle\Tests\GraphQL\DataObjectInputProcessor;

use Codeception\Test\Unit;
use Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectInputProcessor\Classificationstore;
use Pimcore\Bundle\DataHubBundle\Tests\GraphQL\ServiceStubTrait;

/**
 * @internal
 */
class ClassificationstoreAcceptedTypesTest extends Unit
{
    use ServiceStubTrait;

    private function createProcessor(array $readable): Classificationstore
    {
        $service = $this->createServiceStub();
        $service->setSupportedCsFeatureQueryDataTypes($readable);
        $processor = new Classificationstore(['attributes' => ['attribute' => 'csfield']]);
        $processor->setGraphQLService($service);

        return $processor;
    }

    public function testTheAcceptedSetIsTheReadableSetWithoutTheCalculatedValue(): void
    {
        $processor = $this->createProcessor(['input', 'calculatedValue', 'multiValue']);

        $this->assertSame(['input', 'multiValue'], $processor->getAcceptedTypes());
    }

    public function testARegisteredGeneratorMakesATypeWritableAndAMissingOneRefusesIt(): void
    {
        $this->assertContains('rgbaColor', $this->createProcessor(['input', 'rgbaColor'])->getAcceptedTypes());
        $this->assertNotContains('rgbaColor', $this->createProcessor(['input'])->getAcceptedTypes());
        // a type without a read generator, here table, is not in the set
        $this->assertNotContains('table', $this->createProcessor(['input'])->getAcceptedTypes());
    }
}
