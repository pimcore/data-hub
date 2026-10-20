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
use Pimcore\Bundle\DataHubBundle\DependencyInjection\Compiler\CustomDocumentTypePass;
use Pimcore\Bundle\DataHubBundle\GraphQL\DocumentType\DocumentType;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Custom document types must be registered as references to the shared type service, see
 * ImportExportLocatorsPassTest: an inline Definition makes the schema contain the type twice.
 */
class CustomDocumentTypePassTest extends TestCase
{
    public function testCustomTypesAreRegisteredAsReferences(): void
    {
        $container = new ContainerBuilder();
        $container->setDefinition(DocumentType::class, new Definition(DocumentType::class));
        $container->setDefinition(
            'pme.test.custom_type',
            (new Definition(\stdClass::class))->addTag('pimcore.datahub.graphql.documenttype.customtype', ['id' => 'document_custom'])
        );

        (new CustomDocumentTypePass())->process($container);

        $registered = null;
        foreach ($container->getDefinition(DocumentType::class)->getMethodCalls() as [$method, $arguments]) {
            if ($method === 'registerCustomDataType') {
                $registered = $arguments[0];
            }
        }

        $this->assertIsArray($registered);
        $this->assertArrayHasKey('document_custom', $registered);
        $this->assertInstanceOf(Reference::class, $registered['document_custom']);
        $this->assertSame('pme.test.custom_type', (string)$registered['document_custom']);
    }
}
