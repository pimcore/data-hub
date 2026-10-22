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

namespace Pimcore\Bundle\DataHubBundle\Tests\GraphQL;

use PHPUnit\Framework\TestCase;
use Pimcore\Bundle\DataHubBundle\DependencyInjection\Compiler\ImportExportLocatorsPass;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * The element type registries must receive references to the shared type services. A Definition
 * would be an inline copy, which stays a second instance as soon as the shared service is kept
 * in the container (framework.test) and makes the schema contain the same type name twice.
 */
class ImportExportLocatorsPassTest extends TestCase
{
    private const REGISTRIES = [
        'pimcore.datahub.graphql.assettype' => 'registerAssetDataTypes',
        'pimcore.datahub.graphql.translationtype' => 'registerTranslationDataTypes',
        'pimcore.datahub.graphql.dataobjecttype' => 'registerDataObjectDataTypes',
        'pimcore.datahub.graphql.documenttype' => 'registerDocumentDataTypes',
        'pimcore.datahub.graphql.cstype' => 'registerClassificationStoreDataTypes',
        'pimcore.datahub.graphql.propertytype' => 'registerPropertyDataTypes',
    ];

    public function testElementTypesAreRegisteredAsReferences(): void
    {
        foreach (self::REGISTRIES as $tag => $methodCall) {
            $this->assertRegisteredAsReference($tag, $methodCall);
        }
    }

    private function assertRegisteredAsReference(string $tag, string $methodCall): void
    {
        $container = new ContainerBuilder();
        $container->setDefinition(Service::class, new Definition(Service::class));
        $container->setDefinition(
            'pme.test.type',
            (new Definition(\stdClass::class))->addTag($tag, ['id' => 'document_email'])
        );

        (new ImportExportLocatorsPass())->process($container);

        $registered = null;
        foreach ($container->getDefinition(Service::class)->getMethodCalls() as [$method, $arguments]) {
            if ($method === $methodCall) {
                $registered = $arguments[0];
            }
        }

        $this->assertIsArray($registered, $methodCall . ' is not registered');
        $this->assertArrayHasKey('document_email', $registered);
        $this->assertInstanceOf(Reference::class, $registered['document_email']);
        $this->assertSame('pme.test.type', (string)$registered['document_email']);
    }
}
