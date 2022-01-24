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
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DocumentType;

use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;

class LinkType extends AbstractDocumentType
{
    use ServiceTrait;

    /**
     * @param Service $graphQlService
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
                'resolve' => [$resolver, 'resolveObject']
                ],
            'direct' => Type::string(),
            'linktype' => Type::string(),
            'href' => Type::string()
            ]
        );
    }
}
