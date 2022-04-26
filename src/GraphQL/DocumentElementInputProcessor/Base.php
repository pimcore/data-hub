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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementInputProcessor;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Model\Document\Editable\Loader\EditableLoaderInterface;
use Pimcore\Model\Document\PageSnippet;

abstract class Base
{
    use ServiceTrait;

    /**
     * @var EditableLoaderInterface
     */
    protected $editableLoader;

    /**
     * @param EditableLoaderInterface $editableLoader
     * @param Service $graphQlService
     */
    public function __construct(EditableLoaderInterface $editableLoader, Service $graphQlService)
    {
        $this->editableLoader = $editableLoader;
        $this->graphQlService = $graphQlService;
    }

    /**
     * @param PageSnippet $document
     * @param mixed $newValue
     * @param array $args
     * @param mixed $context
     * @param ResolveInfo $info
     */
    public function process($document, $newValue, $args, $context, ResolveInfo $info)
    {
        $editableName = $newValue['_editableName'];
        $editableType = $newValue['_editableType'];

        $text = $newValue['text'];

        $editable = $this->editableLoader->build($editableType);
        $editable->setName($editableName);
        $editable->setDataFromResource($text);                   // this should be at least valid for input, wysiwyg

        if (method_exists($document, 'setElement')) {
            $document->setElement($editableName, $editable);
        } else {
            $document->setEditable($editable);
        }
    }
}
