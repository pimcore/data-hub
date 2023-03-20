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

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;

class DocumentTranslationType extends ObjectType
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
