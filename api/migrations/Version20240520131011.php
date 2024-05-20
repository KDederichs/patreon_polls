<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240520131011 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE patreon_poll_tier_vote_config DROP CONSTRAINT FK_EF97E06BEE65F0');
        $this->addSql('ALTER TABLE patreon_poll_tier_vote_config ADD CONSTRAINT FK_EF97E06BEE65F0 FOREIGN KEY (campaign_tier_id) REFERENCES patreon_campaign_tier (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE patreon_poll_tier_vote_config DROP CONSTRAINT fk_ef97e06bee65f0');
        $this->addSql('ALTER TABLE patreon_poll_tier_vote_config ADD CONSTRAINT fk_ef97e06bee65f0 FOREIGN KEY (campaign_tier_id) REFERENCES patreon_poll (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
