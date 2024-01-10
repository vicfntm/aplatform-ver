<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230906150319 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commodity ADD commodity_source_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE commodity ADD author_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN commodity.commodity_source_id IS \'(DC2Type:ulid)\'');
        $this->addSql('COMMENT ON COLUMN commodity.author_id IS \'(DC2Type:ulid)\'');
        $this->addSql('ALTER TABLE commodity ADD CONSTRAINT FK_5E8D2F74C19ED70D FOREIGN KEY (commodity_source_id) REFERENCES commodity (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE commodity ADD CONSTRAINT FK_5E8D2F74F675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_5E8D2F74C19ED70D ON commodity (commodity_source_id)');
        $this->addSql('CREATE INDEX IDX_5E8D2F74F675F31B ON commodity (author_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE commodity DROP CONSTRAINT FK_5E8D2F74C19ED70D');
        $this->addSql('ALTER TABLE commodity DROP CONSTRAINT FK_5E8D2F74F675F31B');
        $this->addSql('DROP INDEX IDX_5E8D2F74C19ED70D');
        $this->addSql('DROP INDEX IDX_5E8D2F74F675F31B');
        $this->addSql('ALTER TABLE commodity DROP commodity_source_id');
        $this->addSql('ALTER TABLE commodity DROP author_id');
    }
}
