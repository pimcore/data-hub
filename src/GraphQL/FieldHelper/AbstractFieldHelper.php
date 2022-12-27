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

use GraphQL\Language\AST\ArgumentNode;
use GraphQL\Language\AST\FieldNode;
use GraphQL\Language\AST\FragmentSpreadNode;
use GraphQL\Language\AST\InlineFragmentNode;
use GraphQL\Language\AST\NodeList;
use GraphQL\Language\AST\SelectionSetNode;
use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;
use Pimcore\Model\Element\ElementInterface;

abstract class AbstractFieldHelper
{
    use ServiceTrait;

    public function __construct()
    {
    }

    /**
     * @param object $container
     * @param string $astName
     *
     * @return bool
     */
    public function skipField($container, $astName)
    {
        return false;
    }

    /**
     * @param FieldNode $ast
     * @param array $data
     * @param object $container
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo $resolveInfo
     */
    public function doExtractData(FieldNode $ast, &$data, $container, $args, $context, $resolveInfo = null)
    {
        $astName = $ast->name->value;

        // sometimes we just want to expand relations just to throw them away afterwards because not requested
        if ($this->skipField($container, $astName)) {
            return;
        }

        // example for http://webonyx.github.io/graphql-php/error-handling/
//         throw new MySafeException("fieldhelper", "TBD customized error message");

        $getter = 'get' . ucfirst($astName);
        $arguments = $this->getArguments($ast);
        $languageArgument = isset($arguments['language']) ? $arguments['language'] : null;

        if (method_exists($container, $getter)) {
            if ($languageArgument) {
                if ($ast->alias) {
                    // defer it
                    $data[$astName] = function ($source, $args, $context, ResolveInfo $info) use (
                        $container,
                        $getter
                    ) {
                        return $container->$getter($args['language'] ?? null);
                    };
                } else {
                    $data[$astName] = $container->$getter($languageArgument);
                }
            } else {
                $data[$astName] = $container->$getter();
            }
        }
    }

    /**
     * @param FieldNode $ast
     *
     * @return array
     */
    public function getArguments(FieldNode $ast)
    {
        $result = [];
        $nodeList = $ast->arguments;
        $count = $nodeList->count();
        for ($i = 0; $i < $count; $i++) {
            /** @var ArgumentNode $argumentNode */
            $argumentNode = $nodeList[$i];
            $value = $argumentNode->value->kind === 'ListValue' ? $argumentNode->value->values : $argumentNode->value->value;
            $result[$argumentNode->name->value] = $value;
        }

        return $result;
    }

    /**
     * @param mixed $data
     * @param object $container
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     *
     * @return array
     */
    public function extractData(&$data, $container, $args, $context = [], ResolveInfo $resolveInfo = null)
    {
        if ($container instanceof ElementInterface) {
            // we have to at least add the ID and pass it around even if not requested because we need it internally
            // to resolve fields of linked elements (such as asset image and so on)
            $data['id'] = $container->getId();
        }

        $resolveInfoArray = (array)$resolveInfo;
        $fieldAstList = (array) $resolveInfoArray['fieldNodes'];

        foreach ($fieldAstList as $astNode) {
            if ($astNode instanceof FieldNode) {
                /** @var SelectionSetNode $selectionSet */
                $selectionSet = $astNode->selectionSet;
                if ($selectionSet !== null) {
                    $selections = $selectionSet->selections;
                    $this->processSelections($data, $selections, $container, $args, $context, $resolveInfo);
                }
            }
        }

        return $data;
    }

    /**
     * @param mixed $data
     * @param NodeList|null $selections
     * @param object $container
     * @param array $args
     * @param array $context
     * @param ResolveInfo $resolveInfo
     */
    public function processSelections(&$data, $selections, $container, $args, $context, ResolveInfo $resolveInfo)
    {
        if (!$selections) {
            return;
        }

        foreach ($selections as $selectionNode) {
            if ($selectionNode instanceof FieldNode) {
                $this->doExtractData($selectionNode, $data, $container, $args, $context, $resolveInfo);
            } elseif ($selectionNode instanceof InlineFragmentNode) {
                $inlineSelectionSetNode = $selectionNode->selectionSet;
                /** @var NodeList $inlineSelections */
                $inlineSelections = $inlineSelectionSetNode->selections;
                $count = $inlineSelections->count();
                for ($i = 0; $i < $count; $i++) {
                    $inlineNode = $inlineSelections[$i];
                    if ($inlineNode instanceof FieldNode) {
                        $this->doExtractData($inlineNode, $data, $container, $args, $context, $resolveInfo);
                    }
                }
            } elseif ($selectionNode instanceof FragmentSpreadNode) {
                $fragmentName = $selectionNode->name->value;
                $knownFragments = $resolveInfo->fragments;
                $resolvedFragment = $knownFragments[$fragmentName];
                if ($resolvedFragment) {
                    $fragmentSelectionSet = $resolvedFragment->selectionSet;
                    $fragmentSelections = $fragmentSelectionSet->selections;
                    $this->processSelections($data, $fragmentSelections, $container, $args, $context, $resolveInfo);
                }
            }
        }
    }
}
