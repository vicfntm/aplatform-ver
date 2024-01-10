<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230824165612 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE media (id UUID NOT NULL, product_id UUID DEFAULT NULL, binary_type VARCHAR(255) NOT NULL, binary_source VARCHAR(255) NOT NULL, is_main BOOLEAN NOT NULL, file_path VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6A2CA10C4584665A ON media (product_id)');
        $this->addSql('COMMENT ON COLUMN media.id IS \'(DC2Type:ulid)\'');
        $this->addSql('COMMENT ON COLUMN media.product_id IS \'(DC2Type:ulid)\'');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C4584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE media DROP CONSTRAINT FK_6A2CA10C4584665A');
        $this->addSql('DROP TABLE media');
    }
}
