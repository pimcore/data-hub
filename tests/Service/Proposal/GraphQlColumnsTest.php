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

namespace Pimcore\Bundle\DataHubBundle\Tests\Service\Proposal;

use Codeception\Test\Unit;
use function implode;
use function json_encode;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Bundle\DataHubBundle\Proposal\GraphQl\GraphQlConfigPolicy;
use Pimcore\Bundle\DataHubBundle\Service\Studio\ConfigurationServiceInterface;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Data\Block;
use Pimcore\Model\DataObject\ClassDefinition\Data\Fieldcollections;
use Pimcore\Model\DataObject\ClassDefinition\Data\Input;
use Pimcore\Model\DataObject\ClassDefinition\Data\Localizedfields;
use Pimcore\Model\DataObject\ClassDefinition\Data\Objectbricks;
use Pimcore\Model\DataObject\ClassDefinition\Layout\Panel;
use Pimcore\Model\DataObject\Objectbrick\Definition as ObjectbrickDefinition;
use function sprintf;
use function str_contains;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * The columns of a proposed GraphQL configuration, checked against a real class and brick the
 * way the schema builder resolves them.
 */
class GraphQlColumnsTest extends Unit
{
    use GraphQlColumnsTrait;

    private const string CLASS_NAME = 'ProposalColumnsTestEntity';

    private const string BRICK = 'ProposalColumnsTestBrick';

    private const string STORED = 'columns-test-endpoint';

    private const string AT = 'schema.queryEntities.' . self::CLASS_NAME . '.columnConfig.columns.';

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $claim = new Input();
        $claim->setName('claim');
        $brickLocalized = new Localizedfields();
        $brickLocalized->setName('localizedfields');
        $brickLocalized->setChildren([$claim]);
        $color = new Input();
        $color->setName('color');

        // the brick has to exist before the class references it, and is mapped to the class afterwards
        $brick = new ObjectbrickDefinition();
        $brick->setKey(self::BRICK);
        $brick->setLayoutDefinitions(self::panel(self::BRICK, [$color, $brickLocalized]));
        $brick->save();

        $name = new Input();
        $name->setName('name');
        $title = new Input();
        $title->setName('title');
        $localized = new Localizedfields();
        $localized->setName('localizedfields');
        $localized->setChildren([$title]);
        $bricks = new Objectbricks();
        $bricks->setName('bricks');
        $bricks->setAllowedTypes([self::BRICK]);
        $items = new Fieldcollections();
        $items->setName('items');
        $items->setAllowedTypes([]);
        $note = new Input();
        $note->setName('note');
        $notes = new Block();
        $notes->setName('notes');
        $notes->setChildren([$note]);

        $class = new ClassDefinition();
        $class->setName(self::CLASS_NAME);
        $class->setId('PCTE');
        $class->setLayoutDefinitions(self::panel('pimcore_root', [
            self::panel('Layout', [$name, $localized, $bricks, $items, $notes]),
        ]));
        $class->save();

        $brick = ObjectbrickDefinition::getByKey(self::BRICK);
        $brick->setClassDefinitions([['classname' => self::CLASS_NAME, 'fieldname' => 'bricks']]);
        $brick->save();

        ClassDefinition::getByName(self::CLASS_NAME)->save();
    }

    public static function tearDownAfterClass(): void
    {
        ClassDefinition::getByName(self::CLASS_NAME)?->delete();
        ObjectbrickDefinition::getByKey(self::BRICK)?->delete();

        parent::tearDownAfterClass();
    }

    protected function tearDown(): void
    {
        Configuration::getByName(self::STORED)?->getDao()->delete();
    }

    public function testColumnsTheBuilderResolvesAreProposable(): void
    {
        $descriptor = json_encode(['containerKey' => self::BRICK, 'fieldname' => 'bricks', 'brickfield' => 'claim']);

        $this->assertSame([], $this->problems([
            $this->field('name'),
            $this->field('title'),
            $this->field(self::BRICK . '~color'),
            $this->field(self::BRICK . '~color~bricks'),
            $this->field('?' . $descriptor . '~claim'),
            $this->field('items', 'fieldcollections'),
            $this->field('note'),
            $this->field('key', 'system'),
            $this->field('fullpath', 'system'),
            $this->operator('Concatenator', [$this->field('name'), $this->field('id', 'system')]),
            $this->operator('Alias', [$this->operator('Trimmer', [$this->field('title')])]),
        ]));
    }

    public function testAColumnNamingNoFieldIsRefused(): void
    {
        $problems = $this->problems([$this->field('name'), $this->field('nmae')]);

        $this->assertCount(1, $problems);
        $this->assertStringContainsString(self::AT . '1', $problems[0]);
        $this->assertStringContainsString('"nmae" is not a field of class ' . self::CLASS_NAME, $problems[0]);
    }

    public function testAColumnNamingNoBrickIsRefused(): void
    {
        $problems = $this->problems([$this->field('NoSuchBrick~color')]);

        $this->assertCount(1, $problems);
        $this->assertStringContainsString(self::AT . '0', $problems[0]);
        $this->assertStringContainsString('names the brick "NoSuchBrick"', $problems[0]);
    }

    public function testAColumnNamingNoFieldOfTheBrickIsRefused(): void
    {
        $problems = $this->problems([$this->field(self::BRICK . '~size')]);

        $this->assertCount(1, $problems);
        $this->assertStringContainsString('is not a field of the brick "' . self::BRICK . '"', $problems[0]);
    }

    public function testAnOperatorTheInstallationLacksIsRefused(): void
    {
        $problems = $this->problems([
            $this->operator('NoSuchOperator', [$this->field('name')]),
            $this->operator('Concatenator', [$this->operator('AlsoMissing', [])]),
        ]);

        $this->assertCount(2, $problems);
        $this->assertStringContainsString(
            self::AT . '0: "NoSuchOperator" is not a GraphQL query operator',
            $problems[0],
        );
        $this->assertStringContainsString(
            self::AT . '1.attributes.children.0: "AlsoMissing" is not a GraphQL query operator',
            $problems[1],
        );
    }

    public function testAnOperatorsChildNamingNoFieldIsRefused(): void
    {
        $problems = $this->problems([$this->operator('Trimmer', [$this->field('nope')])]);

        $this->assertCount(1, $problems);
        $this->assertStringContainsString(self::AT . '0.attributes.children.0: "nope"', $problems[0]);
    }

    public function testAMalformedColumnIsRefused(): void
    {
        $problems = $this->problems([
            'name',
            ['attributes' => ['attribute' => 'name', 'dataType' => 'input']],
            ['isOperator' => false, 'attributes' => true],
            ['isOperator' => false, 'attributes' => ['attribute' => 'name']],
            ['isOperator' => true, 'attributes' => ['label' => 'x', 'children' => []]],
            ['isOperator' => true, 'attributes' => ['class' => 'Trimmer', 'children' => 'name']],
        ]);

        $this->assertCount(6, $problems);
        foreach ([0, 1, 2] as $index) {
            $this->assertStringContainsString(self::AT . $index . ' needs "isOperator"', $problems[$index]);
        }
        $this->assertStringContainsString(self::AT . '3 needs "attributes.attribute"', $problems[3]);
        $this->assertStringContainsString(self::AT . '4 is an operator and needs "attributes.class"', $problems[4]);
        $this->assertStringContainsString(self::AT . '5: the operator needs "attributes.children"', $problems[5]);
    }

    public function testColumnsThatAreNoListAreRefused(): void
    {
        $problems = $this->policy()->problems($this->state(['columnConfig' => ['columns' => 'name']]), false);

        $this->assertSame([sprintf(
            'schema.queryEntities.%s.columnConfig needs its columns as a list: {"columns": [...]}.',
            self::CLASS_NAME,
        )], $problems);
    }

    public function testASystemColumnTheEditorDoesNotOfferIsRefused(): void
    {
        $problems = $this->problems([$this->operator('Trimmer', [$this->field('dao', 'system')])]);

        $this->assertCount(1, $problems);
        $this->assertStringContainsString('"dao" is not a system column', $problems[0]);
    }

    public function testAFieldcollectionsColumnOnAnotherFieldIsRefused(): void
    {
        $problems = $this->problems([$this->field('name', 'fieldcollections')]);

        $this->assertCount(1, $problems);
        $this->assertStringContainsString('"name" is not a fieldcollections field', $problems[0]);
    }

    public function testTheColumnsOfAMutationEntityAreCheckedWhereItWrites(): void
    {
        $columns = ['columnConfig' => ['columns' => [$this->field('nmae')]]];
        $problems = $this->policy()->problems([
            'general' => ['name' => 'no-such-endpoint'],
            'schema' => ['mutationEntities' => [
                self::CLASS_NAME => ['id' => self::CLASS_NAME, 'update' => true] + $columns,
            ]],
        ], false);

        $this->problemContaining(
            'schema.mutationEntities.' . self::CLASS_NAME . '.columnConfig.columns.0: "nmae" is not a field',
            $problems,
        );

        // without create or update the builder never reads them
        $this->assertSame([], $this->policy()->problems([
            'general' => ['name' => 'no-such-endpoint'],
            'schema' => ['mutationEntities' => [self::CLASS_NAME => ['id' => self::CLASS_NAME] + $columns]],
        ], false));
    }

    public function testAColumnTheEndpointAlreadyStoresIsNotCheckedAgain(): void
    {
        $dead = $this->field('removedField');
        $document = [
            'general' => ['name' => self::STORED, 'type' => GraphQlConfigPolicy::CONFIG_TYPE, 'path' => ''],
            'schema' => ['queryEntities' => [self::CLASS_NAME => [
                'id' => self::CLASS_NAME,
                'columnConfig' => ['columns' => [$dead]],
            ]]],
        ];
        (new Configuration(GraphQlConfigPolicy::CONFIG_TYPE, '', self::STORED, $document))->getDao()->save();

        $proposed = $document;
        $proposed['schema']['queryEntities'][self::CLASS_NAME]['columnConfig']['columns'] = [
            ['isOperator' => false, 'attributes' => ['dataType' => 'input', 'attribute' => 'removedField']],
            $this->field('alsoMissing'),
        ];
        $problems = $this->policy()->problems($proposed, false);

        $this->assertCount(1, $problems);
        $this->assertStringContainsString(self::AT . '1: "alsoMissing"', $problems[0]);
    }

    /**
     * @param list<mixed> $columns
     *
     * @return list<string>
     */
    private function problems(array $columns): array
    {
        return $this->policy()->problems($this->state(['columnConfig' => ['columns' => $columns]]), false);
    }

    /**
     * @param array<string, mixed> $entity
     *
     * @return array<string, mixed>
     */
    private function state(array $entity): array
    {
        return [
            'general' => ['name' => 'no-such-endpoint'],
            'schema' => ['queryEntities' => [self::CLASS_NAME => ['id' => self::CLASS_NAME] + $entity]],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function field(string $attribute, string $dataType = 'input'): array
    {
        return [
            'isOperator' => false,
            'attributes' => ['attribute' => $attribute, 'label' => $attribute, 'dataType' => $dataType],
        ];
    }

    /**
     * @param list<mixed> $children
     *
     * @return array<string, mixed>
     */
    private function operator(string $class, array $children): array
    {
        return [
            'isOperator' => true,
            'attributes' => ['class' => $class, 'label' => $class, 'type' => 'operator', 'children' => $children],
        ];
    }

    /**
     * @param list<string> $problems
     */
    private function problemContaining(string $needle, array $problems): string
    {
        foreach ($problems as $problem) {
            if (str_contains($problem, $needle)) {
                return $problem;
            }
        }

        $this->fail(sprintf('No problem contains "%s": %s', $needle, implode(' | ', $problems)));
    }

    /**
     * @param list<mixed> $children
     */
    private static function panel(string $name, array $children): Panel
    {
        $panel = new Panel();
        $panel->setName($name);
        $panel->setChildren($children);

        return $panel;
    }

    private function policy(): GraphQlConfigPolicy
    {
        return new GraphQlConfigPolicy(
            $this->createStub(ConfigurationServiceInterface::class),
            new EventDispatcher(),
            $this->graphQlColumns(),
        );
    }
}
