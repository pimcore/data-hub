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

namespace Pimcore\Bundle\DataHubBundle\Migrations\Pimcore2026;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Pimcore\Model\Translation;

/**
 * @internal
 */
final class Version20260506124014 extends AbstractMigration
{
    private const KEYS = [
        'plugin_datahub_config',
        'plugin_datahub_adapter_graphql',
        'plugin_datahub_admin',
    ];

    public function getDescription(): string
    {
        return 'Migrate data-hub backend translations from deprecated admin domain to backend domain';
    }

    public function up(Schema $schema): void
    {
        foreach (self::KEYS as $key) {
            $adminTranslation = Translation::getByKey($key, 'admin');
            if (!$adminTranslation) {
                continue;
            }

            $backendTranslation = Translation::getByKey($key, Translation::DOMAIN_BACKEND, true);
            foreach ($adminTranslation->getTranslations() as $locale => $value) {
                if (!$backendTranslation->hasTranslation($locale)) {
                    $backendTranslation->addTranslation($locale, $value);
                }
            }

            $backendTranslation->save();
            $adminTranslation->delete();

            $this->write('Migrated translation: ' . $key);
        }
    }

    public function down(Schema $schema): void
    {
        foreach (self::KEYS as $key) {
            $backendTranslation = Translation::getByKey($key, Translation::DOMAIN_BACKEND);
            if (!$backendTranslation) {
                continue;
            }

            $adminTranslation = Translation::getByKey($key, 'admin', true);
            foreach ($backendTranslation->getTranslations() as $locale => $value) {
                if (!$adminTranslation->hasTranslation($locale)) {
                    $adminTranslation->addTranslation($locale, $value);
                }
            }

            $adminTranslation->save();
            $backendTranslation->delete();

            $this->write('Reverted translation: ' . $key);
        }
    }
}
