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

namespace Pimcore\Bundle\DataHubBundle\Event\GraphQL\Model;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Model\Listing\AbstractListing;
use Symfony\Contracts\EventDispatcher\Event;

class ListingEvent extends Event
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

    /**
     * @return AbstractListing
     */
    public function getListing(): AbstractListing
    {
        return $this->listing;
    }

    /**
     * @param AbstractListing $listing
     */
    public function setListing(AbstractListing $listing)
    {
        $this->listing = $listing;
    }

    /**
     * @return array
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * @param array $args
     */
    public function setArgs(array $args): void
    {
        $this->args = $args;
    }

    /**
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * @param array $context
     */
    public function setContext(array $context): void
    {
        $this->context = $context;
    }

    /**
     * @return ResolveInfo
     */
    public function getResolveInfo(): ResolveInfo
    {
        return $this->resolveInfo;
    }

    /**
     * @param ResolveInfo $resolveInfo
     */
    public function setResolveInfo(ResolveInfo $resolveInfo): void
    {
        $this->resolveInfo = $resolveInfo;
    }

    /**
     * @param AbstractListing $listing
     * @param array $args
     * @param array $context
     * @param ResolveInfo|null $resolveInfo
     */
    public function __construct(AbstractListing $listing, $args, $context = [], ResolveInfo $resolveInfo = null)
    {
        $this->listing = $listing;
        $this->args = $args;
        $this->context = $context;
        $this->resolveInfo = $resolveInfo;
    }
}
