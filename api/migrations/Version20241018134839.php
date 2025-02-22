<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241018134839 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_2ead6d64a76ed395');
        $this->addSql('DROP INDEX uniq_2ead6d64c94c406');
        $this->addSql('ALTER TABLE patreon_user ADD access_token VARCHAR(64) DEFAULT NULL');
        $this->addSql('ALTER TABLE patreon_user ADD refresh_token VARCHAR(64) DEFAULT NULL');
        $this->addSql('ALTER TABLE patreon_user ADD scope TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE patreon_user ADD token_type TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE patreon_user DROP patreon_access_token');
        $this->addSql('ALTER TABLE patreon_user DROP patreon_refresh_token');
        $this->addSql('ALTER TABLE patreon_user DROP patreon_scope');
        $this->addSql('ALTER TABLE patreon_user DROP patreon_token_type');
        $this->addSql('ALTER TABLE patreon_user RENAME COLUMN patreon_id TO resource_id');
        $this->addSql('ALTER TABLE patreon_user RENAME COLUMN patreon_token_expires_at TO access_token_expires_at');
        $this->addSql('ALTER TABLE patreon_user ALTER access_token_expires_at TYPE TIMESTAMP(6) WITHOUT TIME ZONE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2EAD6D6489329D25 ON patreon_user (resource_id)');
        $this->addSql('CREATE INDEX IDX_2EAD6D64A76ED395 ON patreon_user (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_2EAD6D6489329D25');
        $this->addSql('DROP INDEX IDX_2EAD6D64A76ED395');
        $this->addSql('ALTER TABLE patreon_user ADD patreon_access_token VARCHAR(64) DEFAULT NULL');
        $this->addSql('ALTER TABLE patreon_user ADD patreon_refresh_token VARCHAR(64) DEFAULT NULL');
        $this->addSql('ALTER TABLE patreon_user ADD patreon_scope TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE patreon_user ADD patreon_token_type TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE patreon_user DROP access_token');
        $this->addSql('ALTER TABLE patreon_user DROP refresh_token');
        $this->addSql('ALTER TABLE patreon_user DROP scope');
        $this->addSql('ALTER TABLE patreon_user DROP token_type');
        $this->addSql('ALTER TABLE patreon_user RENAME COLUMN resource_id TO patreon_id');
        $this->addSql('ALTER TABLE patreon_user RENAME COLUMN access_token_expires_at TO patreon_token_expires_at');
        $this->addSql('ALTER TABLE patreon_user ALTER patreon_token_expires_at TYPE TIMESTAMP(6) WITHOUT TIME ZONE');
        $this->addSql('CREATE UNIQUE INDEX uniq_2ead6d64a76ed395 ON patreon_user (user_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_2ead6d64c94c406 ON patreon_user (patreon_id)');
    }
}
