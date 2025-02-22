<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241015151803 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE patreon_user (id UUID NOT NULL, created_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL, patreon_id VARCHAR(64) NOT NULL, patreon_access_token VARCHAR(64) DEFAULT NULL, patreon_refresh_token VARCHAR(64) DEFAULT NULL, patreon_scope TEXT DEFAULT NULL, patreon_token_type TEXT DEFAULT NULL, patreon_token_expires_at TIMESTAMP(6) WITHOUT TIME ZONE DEFAULT NULL, creator BOOLEAN DEFAULT false NOT NULL, user_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2EAD6D64C94C406 ON patreon_user (patreon_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2EAD6D64A76ED395 ON patreon_user (user_id)');
        $this->addSql('ALTER TABLE patreon_user ADD CONSTRAINT FK_2EAD6D64A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE patreon_campaign ADD owner_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE patreon_campaign ADD CONSTRAINT FK_8A5C9DE07E3C61F9 FOREIGN KEY (owner_id) REFERENCES patreon_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8A5C9DE07E3C61F9 ON patreon_campaign (owner_id)');
        $this->addSql('ALTER TABLE patreon_campaign_member ADD patreon_user_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE patreon_campaign_member ADD CONSTRAINT FK_5488CAE451AB6C5D FOREIGN KEY (patreon_user_id) REFERENCES patreon_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_5488CAE451AB6C5D ON patreon_campaign_member (patreon_user_id)');
        $this->addSql('ALTER INDEX uniq_5488cae4f639f77451ab6c5d RENAME TO UNIQ_5488CAE4F639F7742EFFD43C');
        $this->addSql('ALTER INDEX idx_27824168f639f774 RENAME TO IDX_84BCFA45F639F774');
        $this->addSql('ALTER INDEX idx_292b480d3c947c0f RENAME TO IDX_B68343EB3C947C0F');
        $this->addSql('ALTER INDEX idx_292b480db03a8386 RENAME TO IDX_B68343EBB03A8386');
        $this->addSql('ALTER INDEX uniq_292b480d64de5a5 RENAME TO UNIQ_B68343EB64DE5A5');
        $this->addSql('ALTER INDEX idx_b5ab8b203c947c0f RENAME TO IDX_ED568EBE3C947C0F');
        $this->addSql('ALTER INDEX idx_b5ab8b20a7c41d6f RENAME TO IDX_ED568EBEA7C41D6F');
        $this->addSql('ALTER INDEX idx_b5ab8b20865c4f6a RENAME TO IDX_ED568EBE865C4F6A');
        $this->addSql('ALTER INDEX uniq_b5ab8b20a7c41d6f865c4f6a RENAME TO UNIQ_ED568EBEA7C41D6F865C4F6A');
        $this->addSql('ALTER INDEX idx_ef97e06ca51c3c7 RENAME TO IDX_EA0F2A24CA51C3C7');
        $this->addSql('ALTER INDEX idx_ef97e06bee65f0 RENAME TO IDX_EA0F2A24BEE65F0');
        $this->addSql('ALTER INDEX uniq_ef97e06ca51c3c7bee65f0 RENAME TO UNIQ_EA0F2A24CA51C3C7BEE65F0');
        $this->addSql('ALTER TABLE users DROP patreon_access_token');
        $this->addSql('ALTER TABLE users DROP patreon_refresh_token');
        $this->addSql('ALTER TABLE users DROP patreon_scope');
        $this->addSql('ALTER TABLE users DROP patreon_token_type');
        $this->addSql('ALTER TABLE users DROP patreon_token_expires_at');
        $this->addSql('ALTER TABLE users DROP creator');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE patreon_user DROP CONSTRAINT FK_2EAD6D64A76ED395');
        $this->addSql('DROP TABLE patreon_user');
        $this->addSql('ALTER TABLE patreon_campaign_member DROP CONSTRAINT FK_5488CAE451AB6C5D');
        $this->addSql('DROP INDEX IDX_5488CAE451AB6C5D');
        $this->addSql('ALTER TABLE patreon_campaign_member DROP patreon_user_id');
        $this->addSql('ALTER INDEX uniq_5488cae4f639f7742effd43c RENAME TO uniq_5488cae4f639f77451ab6c5d');
        $this->addSql('ALTER TABLE patreon_campaign DROP CONSTRAINT FK_8A5C9DE07E3C61F9');
        $this->addSql('DROP INDEX IDX_8A5C9DE07E3C61F9');
        $this->addSql('ALTER TABLE patreon_campaign DROP owner_id');
        $this->addSql('ALTER INDEX uniq_ea0f2a24ca51c3c7bee65f0 RENAME TO uniq_ef97e06ca51c3c7bee65f0');
        $this->addSql('ALTER INDEX idx_ea0f2a24bee65f0 RENAME TO idx_ef97e06bee65f0');
        $this->addSql('ALTER INDEX idx_ea0f2a24ca51c3c7 RENAME TO idx_ef97e06ca51c3c7');
        $this->addSql('ALTER INDEX idx_84bcfa45f639f774 RENAME TO idx_27824168f639f774');
        $this->addSql('ALTER TABLE users ADD patreon_access_token VARCHAR(64) DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD patreon_refresh_token VARCHAR(64) DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD patreon_scope TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD patreon_token_type TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD patreon_token_expires_at TIMESTAMP(6) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD creator BOOLEAN DEFAULT false NOT NULL');
        $this->addSql('ALTER INDEX uniq_b68343eb64de5a5 RENAME TO uniq_292b480d64de5a5');
        $this->addSql('ALTER INDEX idx_b68343ebb03a8386 RENAME TO idx_292b480db03a8386');
        $this->addSql('ALTER INDEX idx_b68343eb3c947c0f RENAME TO idx_292b480d3c947c0f');
        $this->addSql('ALTER INDEX idx_ed568ebe3c947c0f RENAME TO idx_b5ab8b203c947c0f');
        $this->addSql('ALTER INDEX uniq_ed568ebea7c41d6f865c4f6a RENAME TO uniq_b5ab8b20a7c41d6f865c4f6a');
        $this->addSql('ALTER INDEX idx_ed568ebe865c4f6a RENAME TO idx_b5ab8b20865c4f6a');
        $this->addSql('ALTER INDEX idx_ed568ebea7c41d6f RENAME TO idx_b5ab8b20a7c41d6f');
    }
}
