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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper;

use GraphQL\Language\AST\FieldNode;
use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Logger;
use Pimcore\Model\Asset;

final class DocumentFieldHelper extends AbstractFieldHelper
{
    /**
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
