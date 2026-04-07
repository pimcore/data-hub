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

namespace Pimcore\Bundle\DataHubBundle\Schema;

use OpenApi\Attributes\Items;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;
use Pimcore\Bundle\StudioBackendBundle\Util\Schema\AdditionalAttributesInterface;
use Pimcore\Bundle\StudioBackendBundle\Util\Trait\AdditionalAttributesTrait;

/**
 * @internal
 */
#[Schema(
    schema: 'BundleDataHubConfiguration',
    title: 'Bundle Data Hub Configuration',
    required: [
        'id', 'text', 'type', 'iconCls', 'expandable', 'leaf', 'adapter', 'writable', 'permissions',
        'studioColumnConfig',
        ],
    type: 'object'
)]
final class Configuration implements AdditionalAttributesInterface
{
    use AdditionalAttributesTrait;

    public function __construct(
        #[Property(description: 'ID', type: 'string', example: 'assets')]
        private readonly string $id,
        #[Property(description: 'Text', type: 'string', example: 'assets')]
        private readonly string $text,
        #[Property(description: 'Type', type: 'string', example: 'config')]
        private readonly string $type,
        #[Property(description: 'iconCls', type: 'string', example: 'plugin_pimcore_datahub_icon_graphql')]
        private readonly string $iconCls,
        #[Property(description: 'Expandable', type: 'bool', example: 'false')]
        private readonly bool $expandable,
        #[Property(description: 'Leaf', type: 'bool', example: 'true')]
        private readonly bool $leaf,
        #[Property(description: 'Permissions', type: 'object', example: '{"delete": true, "update": true}')]
        private readonly ?array $permissions = null,
        #[Property(description: 'Allow children', type: 'bool', example: 'false')]
        private readonly bool $allowChildren = false,
        #[Property(description: 'Group', type: 'string', example: 'General Folder')]
        private readonly ?string $group = null,
        #[Property(description: 'Children', type: 'array', items: new Items(self::class))]
        private readonly ?array $children = null,
        #[Property(description: 'Adapter', type: 'string', example: 'graphql')]
        private readonly ?string $adapter = null,
        #[Property(description: 'Writable', type: 'bool', example: 'true')]
        private readonly bool $writable = false,
        #[Property(property: 'studioColumnConfig', description: 'Has Studio Column Configuration', type: 'bool', example: 'false')]
        private readonly bool $hasStudioColumnConfig = false,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getIconCls(): string
    {
        return $this->iconCls;
    }

    public function isExpandable(): bool
    {
        return $this->expandable;
    }

    public function isLeaf(): bool
    {
        return $this->leaf;
    }

    public function getAdapter(): ?string
    {
        return $this->adapter;
    }

    public function isWritable(): bool
    {
        return $this->writable;
    }

    public function hasStudioColumnConfig(): bool
    {
        return $this->hasStudioColumnConfig;
    }

    public function getPermissions(): ?array
    {
        return $this->permissions;
    }

    public function getChildren(): ?array
    {
        return $this->children;
    }

    public function getGroup(): ?string
    {
        return $this->group;
    }

    public function isAllowChildren(): bool
    {
        return $this->allowChildren;
    }
}
