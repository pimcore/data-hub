<?php declare(strict_types=1);

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectMutationFieldConfigGenerator;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectInputProcessor\Classificationstore as ClassificationstoreProcessor;
use Pimcore\Bundle\DataHubBundle\GraphQL\Mutation\MutationType;
use Pimcore\Bundle\DataHubBundle\GraphQL\SharedType\AnyValueType;
use Symfony\Contracts\Service\ResetInterface;

/**
 * @internal
 */
final class Classificationstore extends Base implements ResetInterface
{
    /**
     * The input type is shared by all classification store fields: webonyx/graphql-php rejects a schema
     * containing several distinct type instances with the same name. The cache is cleared on `reset()`
     * (kernel.reset), together with the parents' static input type caches, so every schema build starts again.
     */
    private ?InputObjectType $inputType = null;

    /** {@inheritdoc } */
    public function getGraphQlMutationFieldConfig($nodeDef, $class, $container = null, $params = [])
    {
        $processor = new ClassificationstoreProcessor($nodeDef);
        $processor->setGraphQLService($this->getGraphQlService());

        return [
            'arg' => $this->getInputType(),
            'processor' => [$processor, 'process'],
        ];
    }

    public function reset(): void
    {
        $this->inputType = null;

        // input types cached by the parents embed the types of the previous schema build
        MutationType::$typeCache = [];
        Fieldcollections::$typeCache = [];
    }

    private function getInputType(): InputObjectType
    {
        if ($this->inputType === null) {
            $valueInput = new InputObjectType([
                'name' => 'ClassificationstoreValueInput',
                'fields' => [
                    'value' => [
                        'type' => new AnyValueType(['name' => 'ClassificationstoreValue']),
                        'description' => 'the value as the editor sends it, an explicit null clears the slot',
                    ],
                    'variant' => ['type' => Type::string(), 'description' => 'defaults to the default variant'],
                    'position' => ['type' => Type::int(), 'description' => 'defaults to position 0'],
                ],
            ]);

            $keyInput = new InputObjectType([
                'name' => 'ClassificationstoreKeyInput',
                'fields' => [
                    'keyId' => ['type' => Type::int(), 'description' => 'the numeric id of the key, alternative to `key`'],
                    'key' => ['type' => Type::string(), 'description' => 'the identifier of the key, alternative to `keyId`'],
                    'values' => ['type' => Type::listOf($valueInput)],
                ],
            ]);

            $groupInput = new InputObjectType([
                'name' => 'ClassificationstoreGroupInput',
                'fields' => [
                    'groupId' => ['type' => Type::int(), 'description' => 'the numeric id of the group, alternative to `group`'],
                    'group' => ['type' => Type::string(), 'description' => 'the identifier of the group, alternative to `groupId`'],
                    'language' => ['type' => Type::string(), 'description' => 'defaults to "default"'],
                    'keys' => ['type' => Type::listOf($keyInput)],
                ],
            ]);

            $this->inputType = new InputObjectType([
                'name' => 'ClassificationstoreInput',
                'fields' => [
                    'groups' => ['type' => Type::listOf($groupInput)],
                    'strict' => [
                        'type' => Type::boolean(),
                        'description' => 'if true a write to an inactive group is refused instead of activating the group',
                    ],
                ],
            ]);
        }

        return $this->inputType;
    }
}
