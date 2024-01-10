<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240103165731 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE delivery (id UUID NOT NULL, customer_order_id UUID DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, transport_company VARCHAR(30) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3781EC10A15A2E17 ON delivery (customer_order_id)');
        $this->addSql('COMMENT ON COLUMN delivery.id IS \'(DC2Type:ulid)\'');
        $this->addSql('COMMENT ON COLUMN delivery.customer_order_id IS \'(DC2Type:ulid)\'');
        $this->addSql('ALTER TABLE delivery ADD CONSTRAINT FK_3781EC10A15A2E17 FOREIGN KEY (customer_order_id) REFERENCES "order" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE delivery DROP CONSTRAINT FK_3781EC10A15A2E17');
        $this->addSql('DROP TABLE delivery');
    }
}
