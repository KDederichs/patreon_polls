<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241231151756 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE media_object ADD file_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE media_object ADD file_size INT DEFAULT NULL');
        $this->addSql('ALTER TABLE media_object ADD mime_type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE media_object ADD dimensions JSON NOT NULL');
        $this->addSql('ALTER TABLE media_object ADD original_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE media_object ADD updated_at TIMESTAMP(6) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE media_object ADD uploaded_by_id UUID NOT NULL');
        $this->addSql('ALTER TABLE media_object DROP created_at');
        $this->addSql('ALTER TABLE media_object ADD CONSTRAINT FK_14D43132A2B28FE8 FOREIGN KEY (uploaded_by_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_14D43132A2B28FE8 ON media_object (uploaded_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE media_object DROP CONSTRAINT FK_14D43132A2B28FE8');
        $this->addSql('DROP INDEX IDX_14D43132A2B28FE8');
        $this->addSql('ALTER TABLE media_object ADD created_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE media_object DROP file_name');
        $this->addSql('ALTER TABLE media_object DROP file_size');
        $this->addSql('ALTER TABLE media_object DROP mime_type');
        $this->addSql('ALTER TABLE media_object DROP dimensions');
        $this->addSql('ALTER TABLE media_object DROP original_name');
        $this->addSql('ALTER TABLE media_object DROP updated_at');
        $this->addSql('ALTER TABLE media_object DROP uploaded_by_id');
    }
}
