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

namespace Pimcore\Bundle\DataHubBundle\Tests\GraphQL\DataObjectMutationFieldConfigGenerator;

use Codeception\Test\Unit;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectMutationFieldConfigGenerator\ImageGallery;
use Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper\AssetFieldHelper;
use Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper\DataObjectFieldHelper;
use Pimcore\Bundle\DataHubBundle\GraphQL\FieldHelper\DocumentFieldHelper;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Localization\LocaleServiceInterface;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\Factory;
use Pimcore\Translation\Translator;
use Psr\Container\ContainerInterface;

class ImageGalleryTest extends Unit
{
    /**
     * Regression test for https://github.com/pimcore/pimcore/issues/19139:
     * a class with more than one image gallery field must not end up with
     * several distinct "ImageGalleryInput"/"GalleryImageInput" type instances,
     * which webonyx rejects as non-unique named types.
     */
    public function testMultipleImageGalleryFieldsShareInputTypes(): void
    {
        $generator = new ImageGallery($this->createService());
        $class = new ClassDefinition();

        $first = $generator->getGraphQlMutationFieldConfig(['attributes' => ['attribute' => 'gallery1']], $class);
        $second = $generator->getGraphQlMutationFieldConfig(['attributes' => ['attribute' => 'gallery2']], $class);

        $this->assertSame('ImageGalleryInput', $first['arg']->name);
        $this->assertSame($first['arg'], $second['arg']);

        $schema = new Schema([
            'query' => new ObjectType([
                'name' => 'Query',
                'fields' => ['dummy' => Type::string()],
            ]),
            'mutation' => new ObjectType([
                'name' => 'Mutation',
                'fields' => [
                    'updateGallery1' => [
                        'type' => Type::string(),
                        'args' => ['input' => $first['arg']],
                    ],
                    'updateGallery2' => [
                        'type' => Type::string(),
                        'args' => ['input' => $second['arg']],
                    ],
                ],
            ]),
        ]);

        $schema->assertValid();
    }

    private function createService(): Service
    {
        $factories = new class() implements ContainerInterface {
            public function get(string $id): mixed
            {
                throw new \LogicException(sprintf('Unexpected factory lookup "%s"', $id));
            }

            public function has(string $id): bool
            {
                return false;
            }
        };

        return new Service(
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
    }
}
