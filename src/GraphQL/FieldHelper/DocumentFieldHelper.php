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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper;

use GraphQL\Language\AST\FieldNode;
use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Logger;
use Pimcore\Model\Asset;

class DocumentFieldHelper extends AbstractFieldHelper
{
    /**
     * @param FieldNode $ast
     * @param array $data
     * @param Asset $container
     * @param array $args
     * @param array $context
     * @param ResolveInfo $resolveInfo
     */
    public function doExtractData(FieldNode $ast, &$data, $container, $args, $context, $resolveInfo = null)
    {
        $astName = $ast->name->value;

        // sometimes we just want to expand relations just to throw them away afterwards because not requested
        if ($this->skipField($container, $astName)) {
            return;
        }

        $getter = 'get' . ucfirst($astName);
        $arguments = $this->getArguments($ast);
        $languageArgument = isset($arguments['language']) ? $arguments['language'] : null;

        $realName = $astName;

        if (method_exists($container, $getter)) {
            if ($languageArgument) {
                if ($ast->alias) {
                    // defer it
                    $data[$realName] = function ($source, $args, $context, ResolveInfo $info) use (
                        $container,
                        $getter
                    ) {
                        return $container->$getter($args['language'] ?? null);
                    };
                } else {
                    $data[$realName] = $container->$getter($languageArgument);
                }
            } else {
                try {
                    $data[$realName] = $container->$getter();
                } catch (\Error $e) {
                    Logger::error($e);
                }
            }
        }
    }
}
