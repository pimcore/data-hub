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

use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;
use Pimcore\Bundle\StudioBackendBundle\Util\Schema\AdditionalAttributesInterface;
use Pimcore\Bundle\StudioBackendBundle\Util\Trait\AdditionalAttributesTrait;

/**
 * @internal
 */
#[Schema(
    schema: 'BundleDataHubPermissionUser',
    title: 'Bundle Data Hub Permission User',
    required: ['id', 'text', 'elementType'],
    type: 'object'
)]
final class PermissionUser implements AdditionalAttributesInterface
{
    use AdditionalAttributesTrait;
    public function __construct(
        #[Property(description: 'User or Role ID', type: 'integer', example: 42)]
        private readonly int $id,
        #[Property(description: 'User or Role name', type: 'string', example: 'admin')]
        private readonly string $text,
        #[Property(description: 'Element type', type: 'string', example: 'user')]
        private readonly string $elementType,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getElementType(): string
    {
        return $this->elementType;
    }
}

