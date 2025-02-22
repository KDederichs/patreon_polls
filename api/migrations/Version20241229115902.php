<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241229115902 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_bb280557bee65f0');
        $this->addSql('CREATE INDEX IDX_BB280557BEE65F0 ON patreon_poll_vote_config (campaign_tier_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BB2805573C947C0FBEE65F0 ON patreon_poll_vote_config (poll_id, campaign_tier_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_BB280557BEE65F0');
        $this->addSql('DROP INDEX UNIQ_BB2805573C947C0FBEE65F0');
        $this->addSql('CREATE UNIQUE INDEX uniq_bb280557bee65f0 ON patreon_poll_vote_config (campaign_tier_id)');
    }
}
