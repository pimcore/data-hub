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
use Pimcore\Bundle\DataHubBundle\GraphQL\General\AnyDocumentTargetType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;

/**
 * @internal
 */
final class HardlinkType extends AbstractDocumentType
{
    use ServiceTrait;

    protected $anyDocumentTargetType;

    /**
     * @param array $config
     * @param array $context
     */
    public function __construct(Service $graphQlService, AnyDocumentTargetType $anyDocumentTargetType, $config = ['name' => 'document_hardlink'], $context = [])
    {
        $this->anyDocumentTargetType = $anyDocumentTargetType;
        parent::__construct($graphQlService, $config);
    }

    /**
     * @param array $config
     */
    public function build(&$config)
    {
        $resolver = new \Pimcore\Bundle\DataHubBundle\GraphQL\DocumentResolver\Hardlink();
        $resolver->setGraphQLService($this->getGraphQlService());

        $this->buildBaseFields($config);
        $config['fields'] = array_merge($config['fields'], [
                'sourceId' => Type::int(),
                'propertiesFromSource' => Type::boolean(),
                'childrenFromSource' => Type::boolean(),
                'target' => [
                    'type' => $this->anyDocumentTargetType,
                    'resolve' => [$resolver, 'resolveTarget'],
                ],
            ]
        );
    }
}
