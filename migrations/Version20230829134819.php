<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230829134819 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE smedia_id_seq CASCADE');
        $this->addSql('ALTER TABLE smedia DROP CONSTRAINT fk_907a88dd4584665a');
        $this->addSql('DROP TABLE smedia');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE smedia_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE smedia (id INT NOT NULL, product_id UUID NOT NULL, binary_source VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_907a88dd4584665a ON smedia (product_id)');
        $this->addSql('COMMENT ON COLUMN smedia.product_id IS \'(DC2Type:ulid)\'');
        $this->addSql('ALTER TABLE smedia ADD CONSTRAINT fk_907a88dd4584665a FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
