<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241015151122 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE patreon_poll RENAME TO poll');
        $this->addSql('ALTER TABLE patreon_poll_vote RENAME TO poll_vote');
        $this->addSql('ALTER TABLE patreon_poll_option RENAME TO poll_option');
        $this->addSql('ALTER TABLE patreon_poll_tier_vote_config RENAME TO poll_vote_config');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE poll RENAME TO patreon_poll');
        $this->addSql('ALTER TABLE poll_vote RENAME TO patreon_poll_vote');
        $this->addSql('ALTER TABLE poll_option RENAME TO patreon_poll_option');
        $this->addSql('ALTER TABLE poll_vote_config RENAME TO patreon_poll_tier_vote_config');
    }
}
