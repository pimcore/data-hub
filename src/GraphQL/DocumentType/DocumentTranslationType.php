<?php

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DocumentType;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;

/**
 * @internal
 */
final class DocumentTranslationType extends ObjectType
{
    use ServiceTrait;

    public function __construct(Service $graphQlService, array $config = ['name' => 'document_translation', 'fields' => []])
    {
        $this->setGraphQLService($graphQlService);
        $this->build($config);
        parent::__construct($config);
    }

    /**
     * @param array $config
     */
    public function build(&$config)
    {
        $anyTargetType = $this->graphQlService->buildGeneralType('document_tree');
        $documentResolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\Document(new \Pimcore\Model\Document\Service(), $this->getGraphQlService());

        $config['fields']['id'] = Type::int();
        $config['fields']['language'] = Type::string();
        $config['fields']['target'] = [
            'type' => $anyTargetType,
            'resolve' => [$documentResolver, 'resolveTranslationTarget'],
        ];
    }
}
