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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectType\InputType;

use Pimcore\Bundle\DataHubBundle\GraphQL\Service;

class ImageInputType extends AbstractRelationInputType
{
    /**
     * @param Service $graphQlService
     * @param array $config
     * @param array $context
     */
    public function __construct(Service $graphQlService, $config = ['name' => 'ImageInput'], $context = [])
    {
        parent::__construct($graphQlService, $config, $context);
    }
}
