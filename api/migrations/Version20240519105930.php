<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240519105930 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE media_object (id UUID NOT NULL, created_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE patreon_poll (id UUID NOT NULL, created_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL, poll_name TEXT NOT NULL, ends_at TIMESTAMP(6) WITHOUT TIME ZONE DEFAULT NULL, poll_owner_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_27824168C8A61949 ON patreon_poll (poll_owner_id)');
        $this->addSql('CREATE TABLE patreon_poll_option (id UUID NOT NULL, created_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL, option_name TEXT NOT NULL, poll_id UUID NOT NULL, created_by_id UUID NOT NULL, media_object_id UUID DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_292B480D3C947C0F ON patreon_poll_option (poll_id)');
        $this->addSql('CREATE INDEX IDX_292B480DB03A8386 ON patreon_poll_option (created_by_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_292B480D64DE5A5 ON patreon_poll_option (media_object_id)');
        $this->addSql('CREATE TABLE patreon_poll_vote (id UUID NOT NULL, created_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL, option_id UUID NOT NULL, voted_by_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B5AB8B20A7C41D6F ON patreon_poll_vote (option_id)');
        $this->addSql('CREATE INDEX IDX_B5AB8B20865C4F6A ON patreon_poll_vote (voted_by_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B5AB8B20A7C41D6F865C4F6A ON patreon_poll_vote (option_id, voted_by_id)');
        $this->addSql('CREATE TABLE users (id UUID NOT NULL, created_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL, patreon_id VARCHAR(64) DEFAULT NULL, patreon_access_token VARCHAR(64) DEFAULT NULL, patreon_refresh_token VARCHAR(64) DEFAULT NULL, patreon_scope TEXT DEFAULT NULL, patreon_token_type TEXT DEFAULT NULL, username TEXT NOT NULL, patreon_token_expires_at TIMESTAMP(6) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9C94C406 ON users (patreon_id)');
        $this->addSql('ALTER TABLE patreon_poll ADD CONSTRAINT FK_27824168C8A61949 FOREIGN KEY (poll_owner_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE patreon_poll_option ADD CONSTRAINT FK_292B480D3C947C0F FOREIGN KEY (poll_id) REFERENCES patreon_poll (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE patreon_poll_option ADD CONSTRAINT FK_292B480DB03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE patreon_poll_option ADD CONSTRAINT FK_292B480D64DE5A5 FOREIGN KEY (media_object_id) REFERENCES media_object (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE patreon_poll_vote ADD CONSTRAINT FK_B5AB8B20A7C41D6F FOREIGN KEY (option_id) REFERENCES patreon_poll_option (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE patreon_poll_vote ADD CONSTRAINT FK_B5AB8B20865C4F6A FOREIGN KEY (voted_by_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE patreon_poll DROP CONSTRAINT FK_27824168C8A61949');
        $this->addSql('ALTER TABLE patreon_poll_option DROP CONSTRAINT FK_292B480D3C947C0F');
        $this->addSql('ALTER TABLE patreon_poll_option DROP CONSTRAINT FK_292B480DB03A8386');
        $this->addSql('ALTER TABLE patreon_poll_option DROP CONSTRAINT FK_292B480D64DE5A5');
        $this->addSql('ALTER TABLE patreon_poll_vote DROP CONSTRAINT FK_B5AB8B20A7C41D6F');
        $this->addSql('ALTER TABLE patreon_poll_vote DROP CONSTRAINT FK_B5AB8B20865C4F6A');
        $this->addSql('DROP TABLE media_object');
        $this->addSql('DROP TABLE patreon_poll');
        $this->addSql('DROP TABLE patreon_poll_option');
        $this->addSql('DROP TABLE patreon_poll_vote');
        $this->addSql('DROP TABLE users');
    }
}
