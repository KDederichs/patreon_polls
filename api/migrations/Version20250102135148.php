<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250102135148 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE subscribestar_poll_vote_config (id UUID NOT NULL, created_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL, number_of_votes INT DEFAULT NULL, voting_power INT NOT NULL, max_option_add INT DEFAULT NULL, add_options BOOLEAN DEFAULT false NOT NULL, limited_votes BOOLEAN DEFAULT false NOT NULL, poll_id UUID NOT NULL, campaign_tier_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4E47E8173C947C0F ON subscribestar_poll_vote_config (poll_id)');
        $this->addSql('CREATE INDEX IDX_4E47E817BEE65F0 ON subscribestar_poll_vote_config (campaign_tier_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4E47E8173C947C0FBEE65F0 ON subscribestar_poll_vote_config (poll_id, campaign_tier_id)');
        $this->addSql('CREATE TABLE subscribestar_subscription (id UUID NOT NULL, created_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL, subscribestar_id VARCHAR(64) NOT NULL, tier_id VARCHAR(64) NOT NULL, content_provider_id VARCHAR(64) NOT NULL, active BOOLEAN NOT NULL, subscribestar_user_id UUID NOT NULL, subscribestar_tier_id UUID DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8608199ACAFAA173 ON subscribestar_subscription (subscribestar_id)');
        $this->addSql('CREATE INDEX IDX_8608199AB3889231 ON subscribestar_subscription (subscribestar_user_id)');
        $this->addSql('CREATE INDEX IDX_8608199AB7B2B878 ON subscribestar_subscription (subscribestar_tier_id)');
        $this->addSql('CREATE TABLE subscribestar_tier (id UUID NOT NULL, created_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL, tier_name TEXT NOT NULL, amount_in_cents INT DEFAULT 0 NOT NULL, subscribestar_tier_id VARCHAR(64) NOT NULL, subscribestar_user_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E830F2ADB7B2B878 ON subscribestar_tier (subscribestar_tier_id)');
        $this->addSql('CREATE INDEX IDX_E830F2ADB3889231 ON subscribestar_tier (subscribestar_user_id)');
        $this->addSql('ALTER TABLE subscribestar_poll_vote_config ADD CONSTRAINT FK_4E47E8173C947C0F FOREIGN KEY (poll_id) REFERENCES poll (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscribestar_poll_vote_config ADD CONSTRAINT FK_4E47E817BEE65F0 FOREIGN KEY (campaign_tier_id) REFERENCES subscribestar_tier (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscribestar_subscription ADD CONSTRAINT FK_8608199AB3889231 FOREIGN KEY (subscribestar_user_id) REFERENCES subscribestar_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscribestar_subscription ADD CONSTRAINT FK_8608199AB7B2B878 FOREIGN KEY (subscribestar_tier_id) REFERENCES subscribestar_tier (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscribestar_tier ADD CONSTRAINT FK_E830F2ADB3889231 FOREIGN KEY (subscribestar_user_id) REFERENCES subscribestar_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscribestar_poll_vote_config DROP CONSTRAINT FK_4E47E8173C947C0F');
        $this->addSql('ALTER TABLE subscribestar_poll_vote_config DROP CONSTRAINT FK_4E47E817BEE65F0');
        $this->addSql('ALTER TABLE subscribestar_subscription DROP CONSTRAINT FK_8608199AB3889231');
        $this->addSql('ALTER TABLE subscribestar_subscription DROP CONSTRAINT FK_8608199AB7B2B878');
        $this->addSql('ALTER TABLE subscribestar_tier DROP CONSTRAINT FK_E830F2ADB3889231');
        $this->addSql('DROP TABLE subscribestar_poll_vote_config');
        $this->addSql('DROP TABLE subscribestar_subscription');
        $this->addSql('DROP TABLE subscribestar_tier');
    }
}
