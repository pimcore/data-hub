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
    public function testImportRejectsWronglyTypedFieldsAsValidationError(
        array $importData,
        string $expectedMessage
    ): void
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
        // The message names the failing field, which is what tells the user what to fix in the file.
        $this->assertStringContainsString($expectedMessage, $exception->getMessage());
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
            // Sections: what imports today stays accepted, see validateImportSectionTypes().
            'all sections are arrays' => [self::withSections([
                'general' => ['active' => true],
                'workspaces' => ['object' => []],
                'permissions' => ['user' => [], 'role' => []],
                'security' => ['method' => 'datahub_apikey'],
                'schema' => ['queryEntities' => []],
            ])],
            'general is null' => [self::withSections(['general' => null])],
            'general is false' => [self::withSections(['general' => false])],
            'workspaces is null' => [self::withSections(['workspaces' => null])],
            'schema is null' => [self::withSections(['schema' => null])],
            'schema is false' => [self::withSections(['schema' => false])],
            'permissions is null' => [self::withSections(['permissions' => null])],
            'permissions is an empty string' => [self::withSections(['permissions' => ''])],
            'permissions is zero' => [self::withSections(['permissions' => 0])],
            'permissions is false' => [self::withSections(['permissions' => false])],
            'security is null' => [self::withSections(['security' => null])],
            'security is zero' => [self::withSections(['security' => 0])],
            'security is false' => [self::withSections(['security' => false])],
        ];
    }

    public static function wronglyTypedFieldsProvider(): array
    {
        $valid = ['type' => 'graphql', 'path' => '', 'name' => 'pme', 'configuration' => []];
        $configurationMessage = 'configuration must be an object';
        $typeMessage = 'Type and name must be strings';

        return [
            'configuration is a string' => [array_merge($valid, ['configuration' => 'foo']), $configurationMessage],
            'configuration is a number' => [array_merge($valid, ['configuration' => 42]), $configurationMessage],
            'configuration is true' => [array_merge($valid, ['configuration' => true]), $configurationMessage],
            'type is an array' => [array_merge($valid, ['type' => ['graphql']]), $typeMessage],
            'name is a number' => [array_merge($valid, ['name' => 42]), $typeMessage],
            'path is an array' => [array_merge($valid, ['path' => []]), $typeMessage],
            'general is a string' => self::rejectedSection('general', 'x'),
            'general is an empty string' => self::rejectedSection('general', ''),
            'general is zero' => self::rejectedSection('general', 0),
            'general is true' => self::rejectedSection('general', true),
            'workspaces is a string' => self::rejectedSection('workspaces', 'x'),
            'workspaces is an empty string' => self::rejectedSection('workspaces', ''),
            'workspaces is zero' => self::rejectedSection('workspaces', 0),
            'workspaces is false' => self::rejectedSection('workspaces', false),
            'workspaces is true' => self::rejectedSection('workspaces', true),
            'permissions is a string' => self::rejectedSection('permissions', 'x'),
            'permissions is a number' => self::rejectedSection('permissions', 1),
            'permissions is true' => self::rejectedSection('permissions', true),
            // The security check reads ['method'] from it, which throws on a string, although the import succeeds.
            'security is an empty string' => self::rejectedSection('security', ''),
            'security is a string' => self::rejectedSection('security', 'x'),
            'security is a number' => self::rejectedSection('security', 1),
            'security is true' => self::rejectedSection('security', true),
            // schema is rejected beyond what fails at import: these import, but the detail endpoint then fails.
            'schema is a string' => self::rejectedSection('schema', 'x'),
            'schema is an empty string' => self::rejectedSection('schema', ''),
            'schema is zero' => self::rejectedSection('schema', 0),
            'schema is a number' => self::rejectedSection('schema', 1),
            'schema is true' => self::rejectedSection('schema', true),
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
     * @return array{0: array, 1: string} the import data and the message part that names the section
     */
    private static function rejectedSection(string $section, mixed $value): array
    {
        return [self::withSections([$section => $value]), 'configuration.' . $section . ' must be an object'];
    }

    /**
     * @param array<string, mixed> $sections
     */
    private static function withSections(array $sections): array
    {
        return ['type' => 'graphql', 'path' => '', 'name' => 'pme', 'configuration' => $sections];
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
