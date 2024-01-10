<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231115081612 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE views (id UUID NOT NULL, product_id UUID NOT NULL, session_id VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_11F09C874584665A ON views (product_id)');
        $this->addSql('COMMENT ON COLUMN views.id IS \'(DC2Type:ulid)\'');
        $this->addSql('COMMENT ON COLUMN views.product_id IS \'(DC2Type:ulid)\'');
        $this->addSql('ALTER TABLE views ADD CONSTRAINT FK_11F09C874584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE views DROP CONSTRAINT FK_11F09C874584665A');
        $this->addSql('DROP TABLE views');
    }
}
