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

namespace Pimcore\Bundle\DataHubBundle\Tests\Service\Studio;

use Codeception\Test\Unit;
use JsonException;
use Pimcore\Bundle\DataHubBundle\Service\Studio\ConfigurationService;
use Pimcore\Bundle\StudioBackendBundle\Exception\Api\ValidationFailedException;

class ConfigurationServiceImportTest extends Unit
{
    /**
     * @dataProvider invalidFileContentProvider
     */
    public function testImportRejectsInvalidFileContentAsValidationError(string $json): void
    {
        try {
            $this->decode($json);
            $this->fail('Expected a ValidationFailedException.');
        } catch (ValidationFailedException $e) {
            $this->assertSame('data-hub.import-config-invalid', $e->getErrorKey());
            $this->assertSame(422, $e->getStatusCode());
        }
    }

    public function testImportKeepsParseErrorOfMalformedJson(): void
    {
        try {
            $this->decode('{"type": ');
            $this->fail('Expected a ValidationFailedException.');
        } catch (ValidationFailedException $e) {
            $this->assertInstanceOf(JsonException::class, $e->getPrevious());
        }
    }

    public function testImportDecodesValidConfigurationFile(): void
    {
        $data = ['type' => 'graphql', 'path' => '', 'name' => 'pme', 'configuration' => []];

        $this->assertSame($data, $this->decode(json_encode($data, JSON_THROW_ON_ERROR)));
    }

    /**
     * @dataProvider wronglyTypedFieldsProvider
     */
    public function testImportRejectsWronglyTypedFieldsAsValidationError(array $importData): void
    {
        $exception = null;

        try {
            // Through the validator the import runs, so removing the field type check from it fails this test.
            $this->validate($importData, 'validateUploadedConfigurationData');
        } catch (ValidationFailedException $e) {
            $exception = $e;
        }

        $this->assertInstanceOf(ValidationFailedException::class, $exception);
        $this->assertSame('data-hub.import-config-invalid', $exception->getErrorKey());
        $this->assertSame(422, $exception->getStatusCode());
    }

    /**
     * @dataProvider acceptedFieldsProvider
     */
    public function testImportAcceptsCorrectlyTypedFields(array $importData): void
    {
        $this->validate($importData);

        $this->addToAssertionCount(1);
    }

    public static function acceptedFieldsProvider(): array
    {
        $valid = ['type' => 'graphql', 'path' => '', 'name' => 'pme', 'configuration' => []];

        return [
            'complete file' => [$valid],
            // Empty values were imported as an empty configuration before this check existed (persist() rebuilds
            // the defaults), so they stay accepted.
            'configuration is missing' => [array_diff_key($valid, ['configuration' => true])],
            'configuration is null' => [array_merge($valid, ['configuration' => null])],
            'configuration is an empty string' => [array_merge($valid, ['configuration' => ''])],
            'configuration is zero' => [array_merge($valid, ['configuration' => 0])],
            'configuration is false' => [array_merge($valid, ['configuration' => false])],
            // A scalar path was stored as a string before this check existed, so it stays accepted.
            'path is a number' => [array_merge($valid, ['path' => 5])],
        ];
    }

    public static function wronglyTypedFieldsProvider(): array
    {
        $valid = ['type' => 'graphql', 'path' => '', 'name' => 'pme', 'configuration' => []];

        return [
            'configuration is a string' => [array_merge($valid, ['configuration' => 'foo'])],
            'configuration is a number' => [array_merge($valid, ['configuration' => 42])],
            'configuration is true' => [array_merge($valid, ['configuration' => true])],
            'type is an array' => [array_merge($valid, ['type' => ['graphql']])],
            'name is a number' => [array_merge($valid, ['name' => 42])],
            'path is an array' => [array_merge($valid, ['path' => []])],
        ];
    }

    public static function invalidFileContentProvider(): array
    {
        return [
            'malformed json' => ['{"type": "graphql", "name": '],
            'empty file' => [''],
            'json string' => ['"just a string"'],
            'json number' => ['42'],
            'json null' => ['null'],
        ];
    }

    /**
     * importConfiguration() needs a booted kernel (writeable check, permissions), so the decoding step it
     * delegates to is called directly. Decoding uses none of the service's collaborators.
     */
    private function decode(string $json): array
    {
        $service = (new \ReflectionClass(ConfigurationService::class))->newInstanceWithoutConstructor();

        return (new \ReflectionMethod(ConfigurationService::class, 'decodeImportData'))->invoke($service, $json);
    }

    /**
     * The type check runs before the name lookup and the adapter check, and uses none of the service's
     * collaborators. Rejected files therefore never reach them, even through validateUploadedConfigurationData();
     * accepted files would, so those call the type check on its own.
     */
    private function validate(array $importData, string $methodName = 'validateImportFieldTypes'): void
    {
        $service = (new \ReflectionClass(ConfigurationService::class))->newInstanceWithoutConstructor();

        $method = new \ReflectionMethod(ConfigurationService::class, $methodName);
        $method->invoke($service, $importData);
    }
}
