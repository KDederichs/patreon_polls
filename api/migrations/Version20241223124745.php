<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241223124745 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE patreon_poll_vote_config (id UUID NOT NULL, created_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL, number_of_votes SMALLINT NOT NULL, voting_power SMALLINT NOT NULL, max_option_add SMALLINT DEFAULT 1 NOT NULL, add_options BOOLEAN DEFAULT false NOT NULL, limited_votes BOOLEAN DEFAULT false NOT NULL, poll_id UUID NOT NULL, campaign_tier_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BB2805573C947C0F ON patreon_poll_vote_config (poll_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BB280557BEE65F0 ON patreon_poll_vote_config (campaign_tier_id)');
        $this->addSql('ALTER TABLE patreon_poll_vote_config ADD CONSTRAINT FK_BB2805573C947C0F FOREIGN KEY (poll_id) REFERENCES poll (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE patreon_poll_vote_config ADD CONSTRAINT FK_BB280557BEE65F0 FOREIGN KEY (campaign_tier_id) REFERENCES patreon_campaign_tier (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE poll_vote_config DROP CONSTRAINT fk_ef97e06ca51c3c7');
        $this->addSql('ALTER TABLE poll_vote_config DROP CONSTRAINT fk_ef97e06bee65f0');
        $this->addSql('DROP TABLE poll_vote_config');
        $this->addSql('ALTER TABLE poll DROP CONSTRAINT fk_27824168f639f774');
        $this->addSql('DROP INDEX idx_84bcfa45f639f774');
        $this->addSql('ALTER TABLE poll DROP campaign_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE poll_vote_config (id UUID NOT NULL, created_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL, number_of_votes SMALLINT NOT NULL, patreon_poll_id UUID NOT NULL, campaign_tier_id UUID NOT NULL, voting_power SMALLINT NOT NULL, max_option_add SMALLINT DEFAULT 1 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_ea0f2a24ca51c3c7bee65f0 ON poll_vote_config (patreon_poll_id, campaign_tier_id)');
        $this->addSql('CREATE INDEX idx_ea0f2a24bee65f0 ON poll_vote_config (campaign_tier_id)');
        $this->addSql('CREATE INDEX idx_ea0f2a24ca51c3c7 ON poll_vote_config (patreon_poll_id)');
        $this->addSql('ALTER TABLE poll_vote_config ADD CONSTRAINT fk_ef97e06ca51c3c7 FOREIGN KEY (patreon_poll_id) REFERENCES poll (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE poll_vote_config ADD CONSTRAINT fk_ef97e06bee65f0 FOREIGN KEY (campaign_tier_id) REFERENCES patreon_campaign_tier (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE patreon_poll_vote_config DROP CONSTRAINT FK_BB2805573C947C0F');
        $this->addSql('ALTER TABLE patreon_poll_vote_config DROP CONSTRAINT FK_BB280557BEE65F0');
        $this->addSql('DROP TABLE patreon_poll_vote_config');
        $this->addSql('ALTER TABLE poll ADD campaign_id UUID NOT NULL');
        $this->addSql('ALTER TABLE poll ADD CONSTRAINT fk_27824168f639f774 FOREIGN KEY (campaign_id) REFERENCES patreon_campaign (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_84bcfa45f639f774 ON poll (campaign_id)');
    }
}
