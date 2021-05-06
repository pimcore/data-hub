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

class TextareaType extends SimpleTextType
{
    protected static $instance;

    public static function getInstance()
    {
        if (!self::$instance) {
            $config = self::getStandardConfig('document_editableTextarea');
            self::$instance = new static($config);
        }

        return self::$instance;
    }
}
