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

namespace Pimcore\Bundle\DataHubBundle\OpenApi\Attribute\Request;

use Attribute;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Schema;

/**
 * @internal
 */
#[Attribute(Attribute::TARGET_METHOD)]
final class FileUploadRequestBody extends RequestBody
{
    public function __construct(
        string $description = 'File upload',
        bool $required = true,
        string $propertyName = 'file',
        string $propertyDescription = 'File to upload',
    ) {
        parent::__construct(
            required: $required,
            content: new MediaType(
                mediaType: 'multipart/form-data',
                schema: new Schema(
                    required: [$propertyName],
                    properties: [
                        new Property(
                            property: $propertyName,
                            description: $propertyDescription,
                            type: 'string',
                            format: 'binary',
                        ),
                    ],
                    type: 'object'
                )
            )
        );

        $this->description = $description;
    }
}

