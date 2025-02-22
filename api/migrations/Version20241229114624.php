<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241229114624 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE patreon_poll_vote_config ALTER number_of_votes TYPE INT');
        $this->addSql('ALTER TABLE patreon_poll_vote_config ALTER number_of_votes DROP NOT NULL');
        $this->addSql('ALTER TABLE patreon_poll_vote_config ALTER voting_power TYPE INT');
        $this->addSql('ALTER TABLE patreon_poll_vote_config ALTER max_option_add TYPE INT');
        $this->addSql('ALTER TABLE patreon_poll_vote_config ALTER max_option_add DROP DEFAULT');
        $this->addSql('ALTER TABLE patreon_poll_vote_config ALTER max_option_add DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE patreon_poll_vote_config ALTER number_of_votes TYPE SMALLINT');
        $this->addSql('ALTER TABLE patreon_poll_vote_config ALTER number_of_votes SET NOT NULL');
        $this->addSql('ALTER TABLE patreon_poll_vote_config ALTER voting_power TYPE SMALLINT');
        $this->addSql('ALTER TABLE patreon_poll_vote_config ALTER max_option_add TYPE SMALLINT');
        $this->addSql('ALTER TABLE patreon_poll_vote_config ALTER max_option_add SET DEFAULT 1');
        $this->addSql('ALTER TABLE patreon_poll_vote_config ALTER max_option_add SET NOT NULL');
    }
}
