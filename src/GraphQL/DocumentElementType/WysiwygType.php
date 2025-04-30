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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementType;

use GraphQL\Type\Definition\Type;
use Pimcore\Model\Document\Editable\Wysiwyg;

/**
 * @internal
 */
final class WysiwygType extends SimpleTextType
{
    protected static $instance;

    /**
     * @return WysiwygType
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            $config = self::getStandardConfig('document_editableWysiwyg');

            $config['fields']['frontend'] = [
                'type' => Type::string(),
                'resolve' => static fn (Wysiwyg $value) => $value->frontend(),
            ];

            self::$instance = new static($config);
        }

        return self::$instance;
    }
}
