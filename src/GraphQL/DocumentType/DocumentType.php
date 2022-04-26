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

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\UnionType;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Model\Document;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class DocumentType extends UnionType implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    use ServiceTrait;

    protected $types;

    /**
     * @var EmailType
     */
    protected $emailType;

    /**
     * @var LinkType
     */
    protected $linkType;

    /**
     * @var SnippetType
     */
    protected $snippetType;

    /**
     * @var HardlinkType
     */
    protected $hardlinkType;

    /**
     * @var PageType
     */
    protected $pageType;

    /**
     * @var array
     */
    protected $customTypes;

    /**
     * @param Service $graphQlService
     * @param PageType $pageType
     * @param LinkType $linkType
     * @param EmailType $emailType
     * @param HardlinkType $hardlinkType
     * @param SnippetType $snippetType
     * @param array $config
     */
    public function __construct(Service $graphQlService, PageType $pageType, LinkType $linkType, EmailType $emailType, HardlinkType $hardlinkType, SnippetType $snippetType, $config = [])
    {
        $this->pageType = $pageType;
        $this->hardlinkType = $hardlinkType;
        $this->linkType = $linkType;
        $this->emailType = $emailType;
        $this->snippetType = $snippetType;

        $this->types = [$emailType, $hardlinkType, $linkType, $pageType, $snippetType];
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
        return array_merge($this->types, $this->customTypes);
    }

    /**
     * @param array $customDataTypes
     */
    public function registerCustomDataType($customDataTypes)
    {
        $this->customTypes = $customDataTypes;
    }

    /**
     * @return array
     */
    public function getCustomDataTypes()
    {
        return $this->customTypes;
    }

    /**
     * @inheritdoc
     */
    public function resolveType($element, $context, ResolveInfo $info)
    {
        $element = Document::getById($element['id']);
        if ($element instanceof Document\Page) {
            return $this->pageType;
        } elseif ($element instanceof Document\Link) {
            return $this->linkType;
        } elseif ($element instanceof Document\Email) {
            return $this->emailType;
        } elseif ($element instanceof Document\Hardlink) {
            return $this->hardlinkType;
        } elseif ($element instanceof Document\Snippet) {
            return $this->snippetType;
        }

        return null;
    }
}
