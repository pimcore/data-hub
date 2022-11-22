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

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;

class DocumentPageInputType extends InputObjectType
{
    use ServiceTrait;

    protected $processors = [];

    /**
     * @param Service $graphQlService
     * @param array $config
     * @param array $context
     */
    public function __construct(Service $graphQlService, $config = ['name' => 'document_page_input'], $context = [])
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
        $service = $this->getGraphQlService();

        $elementTypes = $service->getSupportedDocumentElementMutationDataTypes();
        $elementFields = [];
        $processors = [];
        foreach ($elementTypes as $elementType) {
            $typedef = $service->buildDocumentElementDataMutationType($elementType);
            $elementFields[$elementType] = Type::listOf($typedef['arg']);
            $processors[$elementType] = $typedef['processor'];
        }

        $this->processors = $processors;

        $elementInputTypeList = new InputObjectType([ //TODO this is document_page specific
            'name' => 'document_pagemutationelements',
            'fields' => $elementFields
        ]);

        $config['fields'] = [
            'module' => Type::string(),
            'controller' => Type::string(),
            'action' => Type::string(),
            'template' => Type::string(),
            'elements' => $elementInputTypeList
        ];
    }

    /**
     * @return array
     */
    public function getProcessors()
    {
        return $this->processors;
    }
}
