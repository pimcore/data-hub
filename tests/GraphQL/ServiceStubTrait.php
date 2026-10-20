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

namespace Pimcore\Bundle\DataHubBundle\Tests\GraphQL;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper\AssetFieldHelper;
use Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper\DataObjectFieldHelper;
use Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper\DocumentFieldHelper;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Localization\LocaleServiceInterface;
use Pimcore\Model\Factory;
use Pimcore\Translation\Translator;
use Psr\Container\ContainerInterface;

/**
 * Builds a real Service with stubbed dependencies, so resolvers can be tested without a kernel.
 */
trait ServiceStubTrait
{
    /**
     * Arguments of every Service::extractData() call made by the resolvers.
     */
    private array $extractDataCalls = [];

    /**
     * Provides just what the resolvers use: buildGeneralType('anytarget') and
     * extractData() via the per-element field helpers.
     */
    private function createServiceStub(): Service
    {
        $anyTargetType = new ObjectType([
            'name' => 'service_stub_anytarget',
            'fields' => ['id' => Type::int()],
        ]);

        $factories = new class($anyTargetType) implements ContainerInterface {
            public function __construct(private readonly ObjectType $type)
            {
            }

            public function get(string $id): mixed
            {
                return new class($this->type) {
                    public function __construct(private readonly ObjectType $type)
                    {
                    }

                    public function build(): ObjectType
                    {
                        return $this->type;
                    }
                };
            }

            public function has(string $id): bool
            {
                return true;
            }
        };

        $fieldHelper = new class($this->extractDataCalls) {
            private array $calls;

            public function __construct(array &$calls)
            {
                $this->calls = &$calls;
            }

            public function extractData(...$args): void
            {
                $this->calls[] = $args;
            }
        };

        $service = new Service(
            new AssetFieldHelper(),
            new DocumentFieldHelper(),
            new DataObjectFieldHelper(),
            $this->createMock(LocaleServiceInterface::class),
            new Factory(),
            $this->createMock(Translator::class),
            $factories,
            $factories,
            $factories,
            $factories,
            $factories,
            $factories,
            $factories,
            $factories,
            $factories,
            $factories,
        );

        // swap in the recording field helper (class-scoped access, the
        // concrete helper classes are final and cannot be extended or mocked)
        \Closure::bind(static function (Service $service, object $fieldHelper): void {
            $service->assetFieldHelper = $fieldHelper;
            $service->documentFieldHelper = $fieldHelper;
            $service->objectFieldHelper = $fieldHelper;
        }, null, Service::class)($service, $fieldHelper);

        return $service;
    }
}
