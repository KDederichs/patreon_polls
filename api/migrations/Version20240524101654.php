<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240524101654 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE patreon_campaign_member (id UUID NOT NULL, created_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL, patreon_user_id VARCHAR(64) NOT NULL, campaign_id UUID NOT NULL, tier_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5488CAE4F639F774 ON patreon_campaign_member (campaign_id)');
        $this->addSql('CREATE INDEX IDX_5488CAE4A354F9DC ON patreon_campaign_member (tier_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5488CAE4F639F77451AB6C5D ON patreon_campaign_member (campaign_id, patreon_user_id)');
        $this->addSql('ALTER TABLE patreon_campaign_member ADD CONSTRAINT FK_5488CAE4F639F774 FOREIGN KEY (campaign_id) REFERENCES patreon_campaign (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE patreon_campaign_member ADD CONSTRAINT FK_5488CAE4A354F9DC FOREIGN KEY (tier_id) REFERENCES patreon_campaign_tier (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EF97E06CA51C3C7BEE65F0 ON patreon_poll_tier_vote_config (patreon_poll_id, campaign_tier_id)');
        $this->addSql('ALTER TABLE patreon_poll_vote ADD vote_power SMALLINT DEFAULT 1 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE patreon_campaign_member DROP CONSTRAINT FK_5488CAE4F639F774');
        $this->addSql('ALTER TABLE patreon_campaign_member DROP CONSTRAINT FK_5488CAE4A354F9DC');
        $this->addSql('DROP TABLE patreon_campaign_member');
        $this->addSql('DROP INDEX UNIQ_EF97E06CA51C3C7BEE65F0');
        $this->addSql('ALTER TABLE patreon_poll_vote DROP vote_power');
    }
}
