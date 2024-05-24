<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240524150440 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE member_entitled_tier (id UUID NOT NULL, created_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL, campaign_member_id UUID DEFAULT NULL, tier_id UUID DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_82AD3F31B51FCF85 ON member_entitled_tier (campaign_member_id)');
        $this->addSql('CREATE INDEX IDX_82AD3F31A354F9DC ON member_entitled_tier (tier_id)');
        $this->addSql('CREATE TABLE patreon_campaign_webhook (id UUID NOT NULL, created_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL, patreon_webhook_id VARCHAR(64) NOT NULL, triggers JSON NOT NULL, secret TEXT NOT NULL, campaign_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_73E9A4094B74E54D ON patreon_campaign_webhook (patreon_webhook_id)');
        $this->addSql('CREATE INDEX IDX_73E9A409F639F774 ON patreon_campaign_webhook (campaign_id)');
        $this->addSql('ALTER TABLE member_entitled_tier ADD CONSTRAINT FK_82AD3F31B51FCF85 FOREIGN KEY (campaign_member_id) REFERENCES patreon_campaign_member (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE member_entitled_tier ADD CONSTRAINT FK_82AD3F31A354F9DC FOREIGN KEY (tier_id) REFERENCES patreon_campaign_tier (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE patreon_campaign_webhook ADD CONSTRAINT FK_73E9A409F639F774 FOREIGN KEY (campaign_id) REFERENCES patreon_campaign (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE patreon_campaign_member DROP CONSTRAINT fk_5488cae4a354f9dc');
        $this->addSql('DROP INDEX idx_5488cae4a354f9dc');
        $this->addSql('ALTER TABLE patreon_campaign_member DROP tier_id');
        $this->addSql('ALTER TABLE patreon_campaign_tier ADD amount_in_cents INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE users ADD creator BOOLEAN DEFAULT false NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE member_entitled_tier DROP CONSTRAINT FK_82AD3F31B51FCF85');
        $this->addSql('ALTER TABLE member_entitled_tier DROP CONSTRAINT FK_82AD3F31A354F9DC');
        $this->addSql('ALTER TABLE patreon_campaign_webhook DROP CONSTRAINT FK_73E9A409F639F774');
        $this->addSql('DROP TABLE member_entitled_tier');
        $this->addSql('DROP TABLE patreon_campaign_webhook');
        $this->addSql('ALTER TABLE patreon_campaign_member ADD tier_id UUID NOT NULL');
        $this->addSql('ALTER TABLE patreon_campaign_member ADD CONSTRAINT fk_5488cae4a354f9dc FOREIGN KEY (tier_id) REFERENCES patreon_campaign_tier (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_5488cae4a354f9dc ON patreon_campaign_member (tier_id)');
        $this->addSql('ALTER TABLE users DROP creator');
        $this->addSql('ALTER TABLE patreon_campaign_tier DROP amount_in_cents');
    }
}
