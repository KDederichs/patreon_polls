<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241111202316 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_2ead6d6489329d25');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2EAD6D6489329D25BC06EA63 ON patreon_user (resource_id, creator)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_2EAD6D6489329D25BC06EA63');
        $this->addSql('CREATE UNIQUE INDEX uniq_2ead6d6489329d25 ON patreon_user (resource_id)');
    }
}
