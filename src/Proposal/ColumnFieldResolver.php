<?php

declare(strict_types=1);

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\DataHubBundle\Proposal;

use function count;
use function explode;
use function is_array;
use function is_string;
use function json_decode;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\ClassDefinition\Data\Block;
use Pimcore\Model\DataObject\ClassDefinition\Data\Localizedfields;
use Pimcore\Model\DataObject\Objectbrick\Definition as ObjectbrickDefinition;
use Pimcore\Model\DataObject\Service as DataObjectService;
use function str_contains;
use function str_starts_with;
use function substr;

/**
 * The field definition a column key of a data object column configuration names, read the way
 * the GraphQL schema builder reads it (DataObjectFieldHelper::getFieldDefinitionFromKey), so a
 * proposal can be checked before the builder meets it:
 *
 * - `field`: a field of the class, its localized fields first
 * - `Brick~field` and `Brick~field~objectbricksField`: a field of a brick an objectbricks field
 *   of the class allows
 * - `?{"containerKey": "Brick", ...}~field`: a field of that brick, its localized fields first
 * - a key starting with `~` names nothing the builder resolves
 *
 * Where the builder fails on an unknown brick, this answers no field.
 *
 * @internal
 */
final readonly class ColumnFieldResolver
{
    public function field(ClassDefinition $class, string $key): ?Data
    {
        if ($key === '' || str_starts_with($key, '~')) {
            return null;
        }

        $parts = explode('~', $key);
        if (count($parts) === 1) {
            return $this->ownField($class, $key);
        }

        $brickType = $this->brickType($key);
        $brick = $brickType !== null ? ObjectbrickDefinition::getByKey($brickType) : null;
        if ($brick === null || DataObjectService::getFieldForBrickType($class, $brickType) === null) {
            return null;
        }

        if (str_contains($parts[0], '?')) {
            $localized = $brick->getFieldDefinition('localizedfields');
            $field = $localized instanceof Localizedfields ? $localized->getFieldDefinition($parts[1]) : null;
            if ($field !== null) {
                return $field;
            }
        }

        return $brick->getFieldDefinition($parts[1]);
    }

    /**
     * The brick a brick key names, whether or not it exists; null for a key that names no brick.
     */
    public function brickType(string $key): ?string
    {
        $parts = explode('~', $key);
        if (count($parts) < 2 || $parts[0] === '') {
            return null;
        }

        if (!str_contains($parts[0], '?')) {
            return $parts[0];
        }

        $descriptor = json_decode(substr($parts[0], 1), true);
        $brickType = is_array($descriptor) ? ($descriptor['containerKey'] ?? null) : null;

        return is_string($brickType) && $brickType !== '' ? $brickType : null;
    }

    /**
     * The block whose entries hold the field: the builder serves such a column through the
     * block's entry type, not as a field of its own.
     */
    public function blockOf(ClassDefinition $class, string $key): ?string
    {
        foreach ($class->getFieldDefinitions(['suppressEnrichment' => true]) as $definition) {
            if (!$definition instanceof Block) {
                continue;
            }

            foreach ($definition->getFieldDefinitions(['suppressEnrichment' => true]) as $child) {
                if ($child->getName() === $key
                    || ($child instanceof Localizedfields
                        && $child->getFieldDefinition($key, ['suppressEnrichment' => true]) !== null)
                ) {
                    return $definition->getName();
                }
            }
        }

        return null;
    }

    private function ownField(ClassDefinition $class, string $key): ?Data
    {
        $localized = $class->getFieldDefinition('localizedfields');
        $field = $localized instanceof Localizedfields ? $localized->getFieldDefinition($key) : null;

        return $field ?? $class->getFieldDefinition($key);
    }
}
