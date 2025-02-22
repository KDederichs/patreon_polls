<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241111152149 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE oauth_state (id UUID NOT NULL, created_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL, auth_type VARCHAR(255) NOT NULL, provider VARCHAR(255) NOT NULL, user_id UUID DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_249038EAA76ED395 ON oauth_state (user_id)');
        $this->addSql('ALTER TABLE oauth_state ADD CONSTRAINT FK_249038EAA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE poll ADD created_by_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE poll ADD CONSTRAINT FK_84BCFA45B03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_84BCFA45B03A8386 ON poll (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE oauth_state DROP CONSTRAINT FK_249038EAA76ED395');
        $this->addSql('DROP TABLE oauth_state');
        $this->addSql('ALTER TABLE poll DROP CONSTRAINT FK_84BCFA45B03A8386');
        $this->addSql('DROP INDEX IDX_84BCFA45B03A8386');
        $this->addSql('ALTER TABLE poll DROP created_by_id');
    }
}
