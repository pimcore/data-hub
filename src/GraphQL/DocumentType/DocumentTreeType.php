<?php
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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DocumentType;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\UnionType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Cache\RuntimeCache;
use Pimcore\Model\Document;
use Pimcore\Model\Element\ElementInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class DocumentTreeType extends UnionType implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    use ServiceTrait;

    private $types;

    private $customTypes = [];

    /**
     * @param Service $graphQlService
     * @param array $config
     */
    public function __construct(Service $graphQlService, $config = ['name' => 'document_tree'])
    {
        $this->setGraphQLService($graphQlService);
        parent::__construct($config);
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    public function getTypes(): array
    {
        $context = RuntimeCache::get('datahub_context');

        $types = [];

        $supportedTypes = [
            '_document_folder',
            'document_email',
            'document_hardlink',
            'document_link',
            'document_page',
            'document_snippet'
        ];
        foreach ($supportedTypes as $supportedType) {
            $this->types[$supportedType] = $this->getGraphQlService()->getDocumentTypeDefinition($supportedType);
            $types[] = $this->types[$supportedType];
        }

        $document = $this->getGraphQlService()->getDocumentTypeDefinition('document');
        if (count($document->getCustomDataTypes())) {
            foreach ($document->getCustomDataTypes() as $ckey => $customType) {
                $this->customTypes[$ckey] = $this->getGraphQlService()->getDocumentTypeDefinition($ckey);
                $types[] = $this->customTypes[$ckey];
            }
        }

        return $types;
    }

    /**
     * @param ElementInterface $element
     * @param array $context
     * @param ResolveInfo $info
     *
     * @return mixed
     */
    public function resolveType($element, $context, ResolveInfo $info)
    {
        $rawElement = $element;
        $element = Document::getById($element['id']);

        if ($element instanceof Document\Folder) {
            return $this->types['_document_folder'];
        }
        if ($element instanceof Document\Page) {
            return $this->types['document_page'];
        }
        if ($element instanceof Document\Link) {
            return $this->types['document_link'];
        }
        if ($element instanceof Document\Email) {
            return $this->types['document_email'];
        }
        if ($element instanceof Document\Hardlink) {
            return $this->types['document_hardlink'];
        }
        if ($element instanceof Document\Snippet) {
            return $this->types['document_snippet'];
        }

        if (count($this->customTypes)) {
            foreach ($this->customTypes as $customType) {
                if ($customType->isTypeof($rawElement, $context, $info)) {
                    return $customType;
                }
            }
        }

        return null;
    }
}
