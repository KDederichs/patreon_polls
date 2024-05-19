<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240519131640 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE patreon_campaign (id UUID NOT NULL, created_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL, patreon_campaign_id VARCHAR(64) NOT NULL, campaign_owner_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8A5C9DE06991FC92 ON patreon_campaign (patreon_campaign_id)');
        $this->addSql('CREATE INDEX IDX_8A5C9DE04C4CB786 ON patreon_campaign (campaign_owner_id)');
        $this->addSql('CREATE TABLE patreon_campaign_tier (id UUID NOT NULL, created_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL, tier_name TEXT NOT NULL, patreon_tier_id VARCHAR(64) NOT NULL, campaign_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BA8FBE2255914614 ON patreon_campaign_tier (patreon_tier_id)');
        $this->addSql('CREATE INDEX IDX_BA8FBE22F639F774 ON patreon_campaign_tier (campaign_id)');
        $this->addSql('CREATE TABLE patreon_poll_tier_vote_config (id UUID NOT NULL, created_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL, number_of_votes SMALLINT NOT NULL, patreon_poll_id UUID NOT NULL, campaign_tier_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EF97E06CA51C3C7 ON patreon_poll_tier_vote_config (patreon_poll_id)');
        $this->addSql('CREATE INDEX IDX_EF97E06BEE65F0 ON patreon_poll_tier_vote_config (campaign_tier_id)');
        $this->addSql('ALTER TABLE patreon_campaign ADD CONSTRAINT FK_8A5C9DE04C4CB786 FOREIGN KEY (campaign_owner_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE patreon_campaign_tier ADD CONSTRAINT FK_BA8FBE22F639F774 FOREIGN KEY (campaign_id) REFERENCES patreon_campaign (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE patreon_poll_tier_vote_config ADD CONSTRAINT FK_EF97E06CA51C3C7 FOREIGN KEY (patreon_poll_id) REFERENCES patreon_poll (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE patreon_poll_tier_vote_config ADD CONSTRAINT FK_EF97E06BEE65F0 FOREIGN KEY (campaign_tier_id) REFERENCES patreon_poll (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE patreon_poll DROP CONSTRAINT fk_27824168c8a61949');
        $this->addSql('DROP INDEX idx_27824168c8a61949');
        $this->addSql('ALTER TABLE patreon_poll RENAME COLUMN poll_owner_id TO campaign_id');
        $this->addSql('ALTER TABLE patreon_poll ADD CONSTRAINT FK_27824168F639F774 FOREIGN KEY (campaign_id) REFERENCES patreon_campaign (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_27824168F639F774 ON patreon_poll (campaign_id)');
        $this->addSql('ALTER TABLE patreon_poll_vote ADD poll_id UUID NOT NULL');
        $this->addSql('ALTER TABLE patreon_poll_vote ADD CONSTRAINT FK_B5AB8B203C947C0F FOREIGN KEY (poll_id) REFERENCES patreon_poll (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_B5AB8B203C947C0F ON patreon_poll_vote (poll_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE patreon_campaign DROP CONSTRAINT FK_8A5C9DE04C4CB786');
        $this->addSql('ALTER TABLE patreon_campaign_tier DROP CONSTRAINT FK_BA8FBE22F639F774');
        $this->addSql('ALTER TABLE patreon_poll_tier_vote_config DROP CONSTRAINT FK_EF97E06CA51C3C7');
        $this->addSql('ALTER TABLE patreon_poll_tier_vote_config DROP CONSTRAINT FK_EF97E06BEE65F0');
        $this->addSql('DROP TABLE patreon_campaign');
        $this->addSql('DROP TABLE patreon_campaign_tier');
        $this->addSql('DROP TABLE patreon_poll_tier_vote_config');
        $this->addSql('ALTER TABLE patreon_poll DROP CONSTRAINT FK_27824168F639F774');
        $this->addSql('DROP INDEX IDX_27824168F639F774');
        $this->addSql('ALTER TABLE patreon_poll RENAME COLUMN campaign_id TO poll_owner_id');
        $this->addSql('ALTER TABLE patreon_poll ADD CONSTRAINT fk_27824168c8a61949 FOREIGN KEY (poll_owner_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_27824168c8a61949 ON patreon_poll (poll_owner_id)');
        $this->addSql('ALTER TABLE patreon_poll_vote DROP CONSTRAINT FK_B5AB8B203C947C0F');
        $this->addSql('DROP INDEX IDX_B5AB8B203C947C0F');
        $this->addSql('ALTER TABLE patreon_poll_vote DROP poll_id');
    }
}
