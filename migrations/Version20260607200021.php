<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260607200021 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_screening_seat ON reservation_seat');
        $this->addSql('ALTER TABLE reservation_seat ADD rowNumber INT NOT NULL, ADD seatNumber INT NOT NULL, DROP `row_number`, DROP seat_number');
        $this->addSql('CREATE UNIQUE INDEX uniq_screening_seat ON reservation_seat (screening_id, rowNumber, seatNumber)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_screening_seat ON reservation_seat');
        $this->addSql('ALTER TABLE reservation_seat ADD `row_number` INT NOT NULL, ADD seat_number INT NOT NULL, DROP rowNumber, DROP seatNumber');
        $this->addSql('CREATE UNIQUE INDEX uniq_screening_seat ON reservation_seat (screening_id, `row_number`, seat_number)');
    }
}
