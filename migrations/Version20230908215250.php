<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230908215250 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "order" (id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN "order".id IS \'(DC2Type:ulid)\'');
        $this->addSql('ALTER TABLE commodity ADD related_order_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN commodity.related_order_id IS \'(DC2Type:ulid)\'');
        $this->addSql('ALTER TABLE commodity ADD CONSTRAINT FK_5E8D2F742B1C2395 FOREIGN KEY (related_order_id) REFERENCES "order" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_5E8D2F742B1C2395 ON commodity (related_order_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE commodity DROP CONSTRAINT FK_5E8D2F742B1C2395');
        $this->addSql('DROP TABLE "order"');
        $this->addSql('DROP INDEX IDX_5E8D2F742B1C2395');
        $this->addSql('ALTER TABLE commodity DROP related_order_id');
    }
}
