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

namespace Pimcore\Bundle\DataHubBundle\Event\GraphQL\Model;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Model\Listing\AbstractListing;
use Symfony\Contracts\EventDispatcher\Event;

final class ListingEvent extends Event
{
    /**
     * @var AbstractListing
     */
    protected $listing;

    /**
     * @var array
     */
    protected $args;

    /**
     * @var array
     */
    protected $context;

    /**
     * @var ResolveInfo
     */
    protected $resolveInfo;

    public function getListing(): AbstractListing
    {
        return $this->listing;
    }

    public function setListing(AbstractListing $listing)
    {
        $this->listing = $listing;
    }

    public function getArgs(): array
    {
        return $this->args;
    }

    public function setArgs(array $args): void
    {
        $this->args = $args;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function setContext(array $context): void
    {
        $this->context = $context;
    }

    public function getResolveInfo(): ResolveInfo
    {
        return $this->resolveInfo;
    }

    public function setResolveInfo(ResolveInfo $resolveInfo): void
    {
        $this->resolveInfo = $resolveInfo;
    }

    /**
     * @param array $args
     * @param array $context
     */
    public function __construct(AbstractListing $listing, $args, $context = [], ?ResolveInfo $resolveInfo = null)
    {
        $this->listing = $listing;
        $this->args = $args;
        $this->context = $context;
        $this->resolveInfo = $resolveInfo;
    }
}
