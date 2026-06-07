<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260607095909 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cinema_hall (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, rows_count INT NOT NULL, seats_per_row INT NOT NULL, cinema_id INT NOT NULL, INDEX IDX_2AA84465B4CB84B6 (cinema_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE cinema_hall ADD CONSTRAINT FK_2AA84465B4CB84B6 FOREIGN KEY (cinema_id) REFERENCES cinema (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cinema_hall DROP FOREIGN KEY FK_2AA84465B4CB84B6');
        $this->addSql('DROP TABLE cinema_hall');
    }
}
