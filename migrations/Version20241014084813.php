<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241014084813 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE color (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE season (id SERIAL NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)');
        $this->addSql('CREATE TABLE vegetable (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE vegetable_color (vegetable_id INT NOT NULL, color_id INT NOT NULL, PRIMARY KEY(vegetable_id, color_id))');
        $this->addSql('CREATE INDEX IDX_39571F9E3D33F4D6 ON vegetable_color (vegetable_id)');
        $this->addSql('CREATE INDEX IDX_39571F9E7ADA1FB5 ON vegetable_color (color_id)');
        $this->addSql('CREATE TABLE vegetable_season (vegetable_id INT NOT NULL, season_id INT NOT NULL, PRIMARY KEY(vegetable_id, season_id))');
        $this->addSql('CREATE INDEX IDX_3EDABE613D33F4D6 ON vegetable_season (vegetable_id)');
        $this->addSql('CREATE INDEX IDX_3EDABE614EC001D1 ON vegetable_season (season_id)');
        $this->addSql('ALTER TABLE vegetable_color ADD CONSTRAINT FK_39571F9E3D33F4D6 FOREIGN KEY (vegetable_id) REFERENCES vegetable (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE vegetable_color ADD CONSTRAINT FK_39571F9E7ADA1FB5 FOREIGN KEY (color_id) REFERENCES color (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE vegetable_season ADD CONSTRAINT FK_3EDABE613D33F4D6 FOREIGN KEY (vegetable_id) REFERENCES vegetable (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE vegetable_season ADD CONSTRAINT FK_3EDABE614EC001D1 FOREIGN KEY (season_id) REFERENCES season (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE vegetable_color DROP CONSTRAINT FK_39571F9E3D33F4D6');
        $this->addSql('ALTER TABLE vegetable_color DROP CONSTRAINT FK_39571F9E7ADA1FB5');
        $this->addSql('ALTER TABLE vegetable_season DROP CONSTRAINT FK_3EDABE613D33F4D6');
        $this->addSql('ALTER TABLE vegetable_season DROP CONSTRAINT FK_3EDABE614EC001D1');
        $this->addSql('DROP TABLE color');
        $this->addSql('DROP TABLE season');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE vegetable');
        $this->addSql('DROP TABLE vegetable_color');
        $this->addSql('DROP TABLE vegetable_season');
    }
}
