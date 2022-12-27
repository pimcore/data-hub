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
declare(strict_types=1);

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace Pimcore\Bundle\DataHubBundle\DependencyInjection\Compiler;

use Pimcore\Bundle\DataHubBundle\GraphQL\DocumentType\DocumentType;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CustomDocumentTypePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
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
