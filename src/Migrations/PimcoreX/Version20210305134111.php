<?php

namespace Pimcore\Bundle\DataHubBundle\Migrations\PimcoreX;

use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\BundleAwareMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20210305134111 extends BundleAwareMigration
{

    protected function getBundleName(): string
    {
        return 'PimcoreDataHubBundle';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // nothing to do
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // nothing to do
    }
}
