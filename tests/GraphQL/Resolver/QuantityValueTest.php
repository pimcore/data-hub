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

namespace Pimcore\Bundle\DataHubBundle\Tests\GraphQL\Resolver;

use Codeception\Test\Unit;
use Pimcore\Bundle\DataHubBundle\GraphQL\Resolver\QuantityValue as QuantityValueResolver;
use Pimcore\Model\DataObject\Data\InputQuantityValue;
use Pimcore\Model\DataObject\Data\QuantityValue;
use Pimcore\Model\DataObject\QuantityValue\Unit as QuantityValueUnit;

class QuantityValueTest extends Unit
{
    private QuantityValueResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new QuantityValueResolver();
    }

    /**
     * Without a unit the whole `unit` field has to resolve to null. Returning an empty array here made
     * graphql-php's default field resolver resolve every requested subfield to null individually, so the
     * response contained `unit: {id: null}` instead of `unit: null`.
     */
    public function testResolveUnitReturnsNullWhenNoUnitIsSet(): void
    {
        $this->assertNull($this->resolver->resolveUnit(new QuantityValue(8500)));
    }

    public function testResolveUnitReturnsNullWhenNoUnitIsSetOnInputQuantityValue(): void
    {
        $this->assertNull($this->resolver->resolveUnit(new InputQuantityValue('8500')));
    }

    public function testResolveUnitReturnsNullForNonQuantityValues(): void
    {
        $this->assertNull($this->resolver->resolveUnit(null));
        $this->assertNull($this->resolver->resolveUnit('not a quantity value'));
    }

    public function testResolveUnitReturnsUnitVarsWhenUnitIsSet(): void
    {
        $unit = new QuantityValueUnit();
        $unit->setId('mm');
        $unit->setAbbreviation('mm');
        $unit->setLongname('Millimeter');

        $resolved = $this->resolver->resolveUnit(new QuantityValue(8500, $unit));

        $this->assertIsArray($resolved);
        $this->assertSame('mm', $resolved['id']);
        $this->assertSame('mm', $resolved['abbreviation']);
        $this->assertSame('Millimeter', $resolved['longname']);
    }
}
