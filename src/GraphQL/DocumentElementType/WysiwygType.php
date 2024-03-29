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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DocumentElementType;

use GraphQL\Type\Definition\Type;
use Pimcore\Model\Document\Editable\Wysiwyg;

class WysiwygType extends SimpleTextType
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
