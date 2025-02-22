<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250102105057 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE subscribestar_user (id UUID NOT NULL, created_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL, resource_id VARCHAR(64) NOT NULL, access_token VARCHAR(64) DEFAULT NULL, refresh_token VARCHAR(64) DEFAULT NULL, scope TEXT DEFAULT NULL, token_type TEXT DEFAULT NULL, access_token_expires_at TIMESTAMP(6) WITHOUT TIME ZONE DEFAULT NULL, creator BOOLEAN DEFAULT false NOT NULL, username TEXT DEFAULT NULL, user_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_413DB36EA76ED395 ON subscribestar_user (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_413DB36E89329D25BC06EA63 ON subscribestar_user (resource_id, creator)');
        $this->addSql('ALTER TABLE subscribestar_user ADD CONSTRAINT FK_413DB36EA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users ADD subscribestar_id VARCHAR(64) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9CAFAA173 ON users (subscribestar_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscribestar_user DROP CONSTRAINT FK_413DB36EA76ED395');
        $this->addSql('DROP TABLE subscribestar_user');
        $this->addSql('DROP INDEX UNIQ_1483A5E9CAFAA173');
        $this->addSql('ALTER TABLE users DROP subscribestar_id');
    }
}
