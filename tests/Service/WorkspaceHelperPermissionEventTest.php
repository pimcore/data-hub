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

namespace Pimcore\Bundle\DataHubBundle\Tests\Service;

use Codeception\Test\Unit;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\Model\PermissionEvent;
use Pimcore\Bundle\DataHubBundle\Event\GraphQL\PermissionEvents;
use Pimcore\Bundle\DataHubBundle\GraphQL\Exception\ClientSafeException;
use Pimcore\Bundle\DataHubBundle\PimcoreDataHubBundle;
use Pimcore\Bundle\DataHubBundle\WorkspaceHelper;
use Pimcore\Cache\RuntimeCache;
use Pimcore\Model\DataObject\Folder;
use ReflectionProperty;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * A PRE_CHECK listener that denies access has to win over a workspace that grants it, for every not allowed policy.
 */
class WorkspaceHelperPermissionEventTest extends Unit
{
    private const CONFIG_NAME = 'permission_event_test';

    private const READ = 'read';

    private const CACHE_KEY = 'datahub_workspace_permissions_' . self::CONFIG_NAME . '_object';

    private ?KernelInterface $previousKernel;

    private int $previousPolicy;

    private EventDispatcher $dispatcher;

    protected function setUp(): void
    {
        $this->previousKernel = \Pimcore::getKernel();
        $this->previousPolicy = PimcoreDataHubBundle::$notAllowedPolicy;

        $this->dispatcher = new EventDispatcher();
        $container = new Container();
        $container->set('event_dispatcher', $this->dispatcher);
        $kernel = $this->createMock(KernelInterface::class);
        $kernel->method('getContainer')->willReturn($container);
        $this->setKernel($kernel);

        $configuration = new Configuration('graphql', '/', self::CONFIG_NAME, ['security' => ['skipPermissionCheck' => false]]);
        RuntimeCache::set(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY, ['configuration' => $configuration]);
    }

    protected function tearDown(): void
    {
        $this->setKernel($this->previousKernel);
        PimcoreDataHubBundle::setNotAllowedPolicy($this->previousPolicy);
        RuntimeCache::set(self::CACHE_KEY, null);
        RuntimeCache::set(PimcoreDataHubBundle::RUNTIME_CONTEXT_KEY, null);
    }

    public function testWorkspaceDecidesWithoutListener(): void
    {
        $this->grantReadOnRoot();
        PimcoreDataHubBundle::setNotAllowedPolicy(PimcoreDataHubBundle::NOT_ALLOWED_POLICY_NULL);

        $this->assertTrue(WorkspaceHelper::checkPermission($this->rootFolder(), self::READ));
    }

    public function testDenyingListenerWinsWithNullPolicy(): void
    {
        $this->grantReadOnRoot();
        $this->denyInListener();
        PimcoreDataHubBundle::setNotAllowedPolicy(PimcoreDataHubBundle::NOT_ALLOWED_POLICY_NULL);

        $this->assertFalse(WorkspaceHelper::checkPermission($this->rootFolder(), self::READ));
    }

    public function testDenyingListenerThrowsWithExceptionPolicy(): void
    {
        $this->grantReadOnRoot();
        $this->denyInListener();
        PimcoreDataHubBundle::setNotAllowedPolicy(PimcoreDataHubBundle::NOT_ALLOWED_POLICY_EXCEPTION);

        $this->expectException(ClientSafeException::class);

        WorkspaceHelper::checkPermission($this->rootFolder(), self::READ);
    }

    public function testListenerReceivesElementAndType(): void
    {
        $this->grantReadOnRoot();
        $root = $this->rootFolder();
        $received = null;
        $this->dispatcher->addListener(
            PermissionEvents::PRE_CHECK,
            static function (PermissionEvent $event) use (&$received): void {
                $received = [$event->getElement(), $event->getType()];
            }
        );

        $this->assertTrue(WorkspaceHelper::checkPermission($root, self::READ));
        $this->assertSame([$root, self::READ], $received);
    }

    public function testWorkspaceStillDeniesWithoutListener(): void
    {
        RuntimeCache::save([], self::CACHE_KEY);
        PimcoreDataHubBundle::setNotAllowedPolicy(PimcoreDataHubBundle::NOT_ALLOWED_POLICY_NULL);

        $this->assertFalse(WorkspaceHelper::checkPermission($this->rootFolder(), self::READ));
    }

    private function grantReadOnRoot(): void
    {
        RuntimeCache::save(
            [1 => ['cid' => 1, 'cpath' => '/', 'create' => 0, self::READ => 1, 'update' => 0, 'delete' => 0]],
            self::CACHE_KEY
        );
    }

    private function denyInListener(): void
    {
        $this->dispatcher->addListener(
            PermissionEvents::PRE_CHECK,
            static fn (PermissionEvent $event) => $event->setIsGranted(false)
        );
    }

    private function rootFolder(): Folder
    {
        $folder = new Folder();
        $folder->setId(1);
        $folder->setKey('');
        $folder->setPath('/');
        $folder->setParent(null);

        return $folder;
    }

    private function setKernel(?KernelInterface $kernel): void
    {
        $property = new ReflectionProperty(\Pimcore::class, 'kernel');
        $property->setValue(null, $kernel);
    }
}
