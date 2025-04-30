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

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Query\Value;

use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ServiceTrait;

abstract class AbstractValue implements ValueInterface
{
    use ServiceTrait;

    /**
     * @var string
     */
    protected $attribute;

    /**
     * @var string
     */
    protected $label;

    /** @var string */
    protected $dataType;

    /**
     * @var mixed
     */
    protected $context;

    /**
     * @param array $config
     * @param array|null $context
     */
    public function __construct($config, $context = null)
    {
        $this->attribute = $config['attribute'];
        $this->label = $config['label'];
        $this->context = $context;
        $this->dataType = $config['dataType'];
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }
}
