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

namespace Pimcore\Bundle\DataHubBundle\Tests\GraphQL\DataObjectInputProcessor;

use Codeception\Test\Unit;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Data\GeoCoordinates;
use Pimcore\Model\DataObject\Data\Link;

/**
 * A key type is only writable when a value written through the mutation reads back unchanged: the untyped value
 * of the input is converted by the key's own editmode conversion, so it is the shape the editor sends.
 *
 * @internal
 */
class ClassificationstoreAddedTypesTest extends Unit
{
    use ClassificationstoreFixtureTrait;

    private const IDENTIFIER_GROUP = 'datahub_added_types_group';

    private const TYPES = [
        'input', 'geopoint', 'geobounds', 'geopolygon', 'geopolyline', 'link', 'image', 'video',
        'manyToOneRelation', 'manyToManyRelation', 'manyToManyObjectRelation',
    ];

    /** @var Asset[] */
    private array $assets = [];

    protected function _after(): void
    {
        $this->deleteFixtures();
        foreach ($this->assets as $asset) {
            $asset->delete();
        }
        $this->assets = [];
    }

    private function createAsset(): Asset\Image
    {
        $asset = new Asset\Image();
        $asset->setFilename('dh-cs-' . uniqid() . '.png');
        $asset->setParent(Asset::getById(1));
        $asset->setData(base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg=='
        ));
        $asset->save();
        $this->assets[] = $asset;

        return $asset;
    }

    /**
     * @return array{success: bool, message: string}
     */
    private function write(Concrete $object, mixed $value): array
    {
        return $this->mutate($object, $this->input($this->groups[0], $this->relations[0], [['value' => $value]]), true);
    }

    private function stored(Concrete $object): mixed
    {
        return $this->store($object)->getLocalizedKeyValue(
            $this->groups[0]->getId(),
            $this->relations[0]->getKeyId(),
            'default'
        );
    }

    private function prepare(string $type): Concrete
    {
        $this->readable = self::TYPES;
        $group = $this->createGroup(self::IDENTIFIER_GROUP);
        $this->createKey($group, 'k', $type);

        return $this->createObject();
    }

    public function testAGeopointRoundTrips(): void
    {
        $object = $this->prepare('geopoint');

        $result = $this->write($object, ['latitude' => 48.2, 'longitude' => 16.3]);

        $this->assertTrue($result['success'], $result['message']);
        $stored = $this->stored($object);
        $this->assertInstanceOf(GeoCoordinates::class, $stored);
        $this->assertSame(48.2, $stored->getLatitude());
        $this->assertSame(16.3, $stored->getLongitude());
    }

    public function testALinkRoundTrips(): void
    {
        $object = $this->prepare('link');

        $result = $this->write($object, ['text' => 'Pimcore', 'path' => 'https://pimcore.com']);

        $this->assertTrue($result['success'], $result['message']);
        $stored = $this->stored($object);
        $this->assertInstanceOf(Link::class, $stored);
        $this->assertSame('Pimcore', $stored->getText());
        $this->assertSame('https://pimcore.com', $stored->getPath());
    }

    public function testAnImageRoundTrips(): void
    {
        $object = $this->prepare('image');
        $asset = $this->createAsset();

        $result = $this->write($object, ['id' => $asset->getId()]);

        $this->assertTrue($result['success'], $result['message']);
        $this->assertSame($asset->getId(), $this->stored($object)?->getId());
    }

    public function testAVideoRoundTrips(): void
    {
        $object = $this->prepare('video');
        $asset = $this->createAsset();

        $result = $this->write($object, ['type' => 'asset', 'data' => $asset->getFullPath()]);

        $this->assertTrue($result['success'], $result['message']);
        $stored = $this->stored($object);
        $this->assertSame('asset', $stored->getType());
        $this->assertSame($asset->getId(), $stored->getData()->getId());
    }

    public function testAManyToOneRelationRoundTrips(): void
    {
        $object = $this->prepare('manyToOneRelation');
        $target = $this->createObject();

        $result = $this->write($object, ['id' => $target->getId(), 'type' => 'object']);

        $this->assertTrue($result['success'], $result['message']);
        $this->assertSame($target->getId(), $this->stored($object)->getId());
    }

    public function testAManyToManyObjectRelationRoundTripsInOrder(): void
    {
        $object = $this->prepare('manyToManyObjectRelation');
        $first = $this->createObject();
        $second = $this->createObject();

        $result = $this->write($object, [
            ['id' => $second->getId(), 'type' => 'object'],
            ['id' => $first->getId(), 'type' => 'object'],
        ]);

        $this->assertTrue($result['success'], $result['message']);
        $this->assertSame(
            [$second->getId(), $first->getId()],
            array_map(static fn ($element) => $element->getId(), $this->stored($object))
        );
    }

    public function testAManyToManyRelationTakesAnyElementType(): void
    {
        $object = $this->prepare('manyToManyRelation');
        $asset = $this->createAsset();
        $target = $this->createObject();

        $result = $this->write($object, [
            ['id' => $target->getId(), 'type' => 'object'],
            ['id' => $asset->getId(), 'type' => 'asset'],
        ]);

        $this->assertTrue($result['success'], $result['message']);
        $this->assertSame(
            ['object', 'asset'],
            array_map(static fn ($element) => $element instanceof Asset ? 'asset' : 'object', $this->stored($object))
        );
    }

    public function testATargetThatDoesNotExistIsRefusedInsteadOfClearingTheSlot(): void
    {
        $object = $this->prepare('manyToOneRelation');

        $result = $this->write($object, ['id' => 99999999, 'type' => 'object']);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('does not exist', $result['message']);
    }

    public function testAMissingTargetInAListIsRefusedInsteadOfStoringAShorterList(): void
    {
        $object = $this->prepare('manyToManyObjectRelation');
        $target = $this->createObject();

        $result = $this->write($object, [
            ['id' => $target->getId(), 'type' => 'object'],
            ['id' => 99999999, 'type' => 'object'],
        ]);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('does not exist', $result['message']);
    }
}
