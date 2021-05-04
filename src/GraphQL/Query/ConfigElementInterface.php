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
 * @category   Pimcore
 * @package    Object
 *
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace Pimcore\Bundle\DataHubBundle\GraphQL\Query;

use GraphQL\Type\Definition\ResolveInfo;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Element\ElementInterface;

interface ConfigElementInterface
{
    /**
     * @return string
     */
    public function getLabel();

    /**
     * @param Concrete $element
     *
     * @return \stdClass|null
     */
    public function getLabeledValue($element, ResolveInfo $resolveInfo = null);
}
