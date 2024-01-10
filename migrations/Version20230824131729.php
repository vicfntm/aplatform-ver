<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230824131729 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commodity (id UUID NOT NULL, product_id UUID NOT NULL, operation_type VARCHAR(255) NOT NULL, amount INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5E8D2F744584665A ON commodity (product_id)');
        $this->addSql('COMMENT ON COLUMN commodity.id IS \'(DC2Type:ulid)\'');
        $this->addSql('COMMENT ON COLUMN commodity.product_id IS \'(DC2Type:ulid)\'');
        $this->addSql('ALTER TABLE commodity ADD CONSTRAINT FK_5E8D2F744584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE price ADD commodity_id UUID NOT NULL');
        $this->addSql('COMMENT ON COLUMN price.commodity_id IS \'(DC2Type:ulid)\'');
        $this->addSql('ALTER TABLE price ADD CONSTRAINT FK_CAC822D9B4ACC212 FOREIGN KEY (commodity_id) REFERENCES commodity (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_CAC822D9B4ACC212 ON price (commodity_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE price DROP CONSTRAINT FK_CAC822D9B4ACC212');
        $this->addSql('ALTER TABLE commodity DROP CONSTRAINT FK_5E8D2F744584665A');
        $this->addSql('DROP TABLE commodity');
        $this->addSql('DROP INDEX IDX_CAC822D9B4ACC212');
        $this->addSql('ALTER TABLE price DROP commodity_id');
    }
}
