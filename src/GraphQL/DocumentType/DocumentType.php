<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PEL
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

    protected $emailType;

    protected $linkType;

    /**
     * @var
     */
    protected $hardlinkType;

    protected $pageType;

    public function __construct(Service $graphQlService, PageType $pageType, LinkType $linkType, EmailType $emailType, HardlinkType $hardlinkType, $config = [])
    {
        $this->pageType = $pageType;
        $this->hardlinkType = $hardlinkType;
        $this->linkType = $linkType;
        $this->emailType = $emailType;

        $this->types = [$emailType, $linkType, $pageType, $hardlinkType];
        $this->setGraphQLService($graphQlService);

        parent::__construct($config);
    }


    /**
     * @return array
     *
     * @throws \Exception
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * @inheritdoc
     */
    public function resolveType($element, $context, ResolveInfo $info)
    {
        $element = Document::getById($element["id"]);
        if ($element instanceof Document\Page) {
            return $this->pageType;
        } else if ($element instanceof Document\Link) {
            return $this->linkType;
        } else if ($element instanceof Document\Email) {
            return $this->emailType;
        } else if ($element instanceof Document\Hardlink) {
            return $this->hardlinkType;
        }

        return null;
    }
}
