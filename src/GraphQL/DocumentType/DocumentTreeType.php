<?php
declare(strict_types=1);

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DocumentType;

use Pimcore\Model\Document;
use Pimcore\Cache\Runtime;
use GraphQL\Type\Definition\UnionType;
use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassTypeDefinitions;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class DocumentTreeType extends UnionType implements ContainerAwareInterface
{

    use ContainerAwareTrait;

    use ServiceTrait;

    private $types;

    /**
     * DocumentTreeType constructor.
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
     * @throws \Exception
     */
    public function getTypes()
    {
        $context = Runtime::get('datahub_context');
        /** @var  $configuration Configuration */
        $configuration = $context["configuration"];

        $types = [];
        if ($configuration->getSpecialEntities()["document_folder"]["read"]) {
            $types[] = $this->getGraphQlService()->getDocumentTypeDefinition("_document_folder");
        }
        if ($configuration->getSpecialEntities()["document"]["read"]) {
            $supportedTypes = [
                'document_email',
                'document_hardlink',
                'document_link',
                'document_page',
                'document_snippet',
                '_document_folder'
            ];
            foreach ($supportedTypes as $supportedType) {
                $this->types[$supportedType] = $this->getGraphQlService()->getDocumentTypeDefinition($supportedType);
                $types[] = $this->types[$supportedType];
            }
        }
        return $types;
    }

    public function resolveType($element, $context, ResolveInfo $info)
    {
        $element = Document::getById($element["id"]);

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

        return null;
    }
}
