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

namespace Pimcore\Bundle\DataHubBundle\Tests\GraphQL\ClassificationstoreType;

use Codeception\Test\Unit;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreFeatureType\Helper;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreType\Feature;
use Pimcore\Bundle\DataHubBundle\GraphQL\ClassificationstoreType\Group;
use Pimcore\Bundle\DataHubBundle\Tests\GraphQL\ServiceStubTrait;
use Pimcore\Model\DataObject\Classificationstore\GroupConfig;
use Pimcore\Model\DataObject\Classificationstore\StoreConfig;

/**
 * The group identifier is published next to the deprecated integer `id` of csGroup. The entries the
 * type resolves are the group arrays built by the classification store field generator
 * (DataObjectQueryFieldConfigGenerator/Classificationstore.php:59-65), so the resolver is exercised
 * with such an array.
 *
 * @internal
 */
class GroupIdentifierTest extends Unit
{
    use ServiceStubTrait;

    private const ID = 'id';

    private const IDENTIFIER = 'identifier';

    /**
     * No group config is saved under this id, so GroupConfig::getById() answers with null.
     */
    private const UNRESOLVABLE_GROUP_ID = 999999999;

    private Group $group;

    /**
     * @var GroupConfig[]
     */
    private array $groupConfigs = [];

    private ?StoreConfig $store = null;

    protected function _before(): void
    {
        $service = $this->createServiceStub();
        $this->group = new Group($service, new Feature($service));
    }

    protected function _after(): void
    {
        foreach ($this->groupConfigs as $groupConfig) {
            $groupConfig->delete();
        }
        $this->groupConfigs = [];

        $this->store?->delete();
        $this->store = null;
    }

    public function testGroupPublishesIdentifierAsNullableStringNextToTheDeprecatedId(): void
    {
        $identifier = $this->group->getField(self::IDENTIFIER);
        $this->assertSame(Type::string(), $identifier->getType());
        $this->assertFalse($identifier->isDeprecated());

        $id = $this->group->getField(self::ID);
        $this->assertSame(Type::int(), $id->getType());
        $this->assertTrue($id->isDeprecated());
        $this->assertSame(Helper::getIdDeprecationReason(), $id->deprecationReason);
    }

    /**
     * `id` keeps being read from the group array by the default field resolver, the deprecation does
     * not put a resolver of its own in front of it.
     */
    public function testIdKeepsResolvingFromTheGroupEntry(): void
    {
        $this->assertNull($this->group->getField(self::ID)->resolveFn);
    }

    public function testIdentifierResolvesToTheIdentifierOfTheGroupConfig(): void
    {
        $identifier = 'datahub_group_identifier';
        $groupId = $this->createGroupConfig($identifier);

        $this->assertSame($identifier, $this->resolveIdentifier($groupId));
    }

    public function testIdentifierResolvesToNullForAGroupWithoutAnIdentifier(): void
    {
        $this->assertNull($this->resolveIdentifier($this->createGroupConfig(null)));
    }

    public function testIdentifierResolvesToNullWhenTheGroupConfigDoesNotResolve(): void
    {
        $this->assertNull($this->resolveIdentifier(self::UNRESOLVABLE_GROUP_ID));
    }

    private function resolveIdentifier(int $groupId): mixed
    {
        $resolve = $this->group->getField(self::IDENTIFIER)->resolveFn;

        return $resolve(['id' => $groupId]);
    }

    private function createGroupConfig(?string $identifier): int
    {
        if ($this->store === null) {
            $this->store = new StoreConfig();
            $this->store->setName('datahub-group-identifier-' . uniqid());
            $this->store->save();
        }

        $groupConfig = new GroupConfig();
        $groupConfig->setStoreId($this->store->getId());
        $groupConfig->setName('datahub-group-identifier-' . uniqid());

        if ($identifier === null) {
            // GroupConfig::save() generates an identifier for every entity it creates, and
            // setIdentifier() takes a non-nullable string, so the one row shape this fixture is
            // about - a group an installation carries from before the backfill, with a NULL
            // identifier - is only reachable through the DAO.
            $groupConfig->getDao()->save();
        } else {
            $groupConfig->setIdentifier($identifier);
            $groupConfig->save();
        }

        $this->groupConfigs[] = $groupConfig;

        return $groupConfig->getId();
    }
}
