<?php

declare(strict_types=1);

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace Pimcore\Bundle\DataHubBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Pimcore\Db;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230127073614 extends AbstractMigration
{
    protected $typeNamespaceMap = [
        'graphql' => 'Pimcore\Bundle\DataHubBundle',
        'simpleRest' => 'Pimcore\Bundle\DataHubSimpleRestBundle'
    ];

    public function getDescription(): string
    {
        return 'Adds the "namespace" column to configurations';
    }

    public function up(Schema $schema): void
    {
        $db = Db::get();
        $configurations = $db->fetchAllAssociative('SELECT * FROM `settings_store` WHERE scope = "pimcore_data_hub"');
        foreach ($configurations as $configuration) {
            $dataArray = json_decode($configuration['data'], true);
            if (!array_key_exists('namespace', $dataArray)) {
                $dataArray['namespace'] = $this->getNamespaceToType($dataArray['type']);
            }
            $configuration['data'] = json_encode($dataArray);
            $this->addSql('UPDATE `settings_store` SET data = :data WHERE id = :id AND scope = "pimcore_data_hub"', $configuration);
        }
    }

    public function down(Schema $schema): void
    {
        $db = Db::get();
        $configurations = $db->fetchAllAssociative('SELECT * FROM `settings_store` WHERE scope = "pimcore_data_hub"');
        foreach ($configurations as $configuration) {
            $dataArray = json_decode($configuration['data'], true);
            if (array_key_exists('namespace', $dataArray)) {
                unset($dataArray['namespace']);
            }

            $configuration['data'] = json_encode($dataArray);
            $this->addSql('UPDATE `settings_store` SET data = :data WHERE id = :id AND scope = "pimcore_data_hub"', $configuration);
        }
    }

    protected function getNamespaceToType(string $type)
    {
        return $this->typeNamespaceMap[$type];
    }
}
