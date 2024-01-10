<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230824132101 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE price DROP CONSTRAINT fk_cac822d94584665a');
        $this->addSql('DROP INDEX idx_cac822d94584665a');
        $this->addSql('ALTER TABLE price DROP product_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE price ADD product_id UUID NOT NULL');
        $this->addSql('COMMENT ON COLUMN price.product_id IS \'(DC2Type:ulid)\'');
        $this->addSql('ALTER TABLE price ADD CONSTRAINT fk_cac822d94584665a FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_cac822d94584665a ON price (product_id)');
    }
}
