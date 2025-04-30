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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementInputProcessor;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\WorkspaceHelper;
use Pimcore\Model\Asset;
use Pimcore\Model\Document\PageSnippet;

/**
 * @internal
 */
final class Image extends Base
{
    /**
     * @param PageSnippet $document
     * @param mixed $newValue
     * @param array $args
     * @param mixed $context
     */
    public function process($document, $newValue, $args, $context, ResolveInfo $info)
    {
        $dataFromEditMode = [];

        $assetId = $newValue['id'];
        $asset = Asset::getById($assetId);
        if (WorkspaceHelper::checkPermission($asset, 'read')) {
            $dataFromEditMode['id'] = $assetId;
        }

        if (isset($newValue['alt'])) {
            $dataFromEditMode['alt'] = $newValue['alt'];
        }

        $editableType = $newValue['_editableType'];

        $editable = $this->editableLoader->build($editableType);

        $editableName = $newValue['_editableName'];
        $editable->setName($editableName);

        $editable->setDataFromEditmode($dataFromEditMode);

        if (method_exists($document, 'setElement')) {
            $document->setElement($editableName, $editable);
        } else {
            $document->setEditable($editable);
        }
    }
}
