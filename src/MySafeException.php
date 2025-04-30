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

namespace Pimcore\Bundle\DataHubBundle;

use GraphQL\Error\ClientAware;

final class MySafeException extends \Exception implements ClientAware
{
    /**
     * @var string|null
     */
    protected $category;

    /**
     * @param string|null $category
     * @param string $message
     * @param int $code
     */
    public function __construct($category = null, $message = '', $code = 0, ?\Throwable $previous = null)
    {
        $this->category = $category;
        parent::__construct($message, $code, $previous);
    }

    public function isClientSafe(): bool
    {
        return true;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category ? $this->category : 'datahub';
    }
}
