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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType\InputType;

use Pimcore\Bundle\DataHubBundle\GraphQL\Service;

/**
 * @internal
 */
final class ImageInputType extends AbstractRelationInputType
{
    /**
     * @param array $config
     * @param array $context
     */
    public function __construct(Service $graphQlService, $config = ['name' => 'ImageInput'], $context = [])
    {
        parent::__construct($graphQlService, $config, $context);
    }
}
