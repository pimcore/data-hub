<?php declare(strict_types=1);

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Bundle\DataHubBundle\Tests\GraphQL\Mutation;

use Codeception\Test\Unit;
use Pimcore\Bundle\DataHubBundle\Tests\GraphQL\DataObjectInputProcessor\ClassificationstoreFixtureTrait;

/**
 * The omitMandatoryCheck argument is forwarded to setOmitMandatoryCheck() before the processors run
 * (MutationType.php:884-886); this asserts what it ends in for a store write.
 *
 * @internal
 */
class ClassificationstoreMandatoryTest extends Unit
{
    use ClassificationstoreFixtureTrait;

    public function testTheFlagSavesAnObjectWhoseMandatoryKeyIsEmptyAndWithoutItTheSaveIsRefused(): void
    {
        $group = $this->createGroup('mandatory_group');
        $mandatory = $this->createKey($group, 'must_have', 'input', [], true, true);
        $optional = $this->createKey($group, 'nice_to_have');
        $object = $this->createObject();
        $input = $this->input($group, $optional, [['value' => 'x']]);

        $refused = $this->mutate($object, $input);
        $this->assertFalse($refused['success']);
        $this->assertStringContainsStringIgnoringCase('mandatory', $refused['message']);

        $omitted = $this->mutate($object, $input, true);
        $this->assertTrue($omitted['success'], $omitted['message']);
        $this->assertNull($this->store($object)->getLocalizedKeyValue($group->getId(), $mandatory->getKeyId(), 'default'));
    }
}
