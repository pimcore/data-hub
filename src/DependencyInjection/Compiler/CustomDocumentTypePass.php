<?php

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */
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

namespace Pimcore\Bundle\DataHubBundle\DependencyInjection\Compiler;

use Pimcore\Bundle\DataHubBundle\GraphQL\DocumentType\DocumentType;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CustomDocumentTypePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $documentTypeService = $container->getDefinition(DocumentType::class);

        $resolvers = $container->findTaggedServiceIds('pimcore.datahub.graphql.documenttype.customtype');

        $dataTypes = [];

        foreach ($resolvers as $id => $tagEntries) {
            foreach ($tagEntries as $tagEntry) {
                $typeDef = $container->getDefinition($id);
                $dataTypes[$tagEntry['id']] = $typeDef;
            }
        }

        $documentTypeService->addMethodCall('registerCustomDataType', [$dataTypes]);
    }
}
