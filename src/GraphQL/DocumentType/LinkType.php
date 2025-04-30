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

use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;

/**
 * @internal
 */
final class LinkType extends AbstractDocumentType
{
    use ServiceTrait;

    /**
     * @param array $config
     * @param array $context
     */
    public function __construct(Service $graphQlService, $config = ['name' => 'document_link'], $context = [])
    {
        parent::__construct($graphQlService, $config);
    }

    /**
     * @param array $config
     */
    public function build(&$config)
    {
        $resolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\DocumentResolver\Link($this->getGraphQlService());
        $resolver->setGraphQLService($this->getGraphQlService());

        $graphQlService = $this->getGraphQlService();
        $anyTargetType = $graphQlService->buildGeneralType('anytarget');

        $this->buildBaseFields($config);
        $config['fields'] = array_merge($config['fields'], [
            'internal' => Type::int(),
            'internalType' => Type::string(),
            'object' => [
                'type' => $anyTargetType,
                'resolve' => [$resolver, 'resolveObject'],
                ],
            'direct' => Type::string(),
            'linktype' => Type::string(),
            'href' => Type::string(),
            ]
        );
    }
}
