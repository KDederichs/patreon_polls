<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250102155510 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscribestar_subscription ADD subscribed_to_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE subscribestar_subscription ADD CONSTRAINT FK_8608199AF9B6176 FOREIGN KEY (subscribed_to_id) REFERENCES subscribestar_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8608199AF9B6176 ON subscribestar_subscription (subscribed_to_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscribestar_subscription DROP CONSTRAINT FK_8608199AF9B6176');
        $this->addSql('DROP INDEX IDX_8608199AF9B6176');
        $this->addSql('ALTER TABLE subscribestar_subscription DROP subscribed_to_id');
    }
}
