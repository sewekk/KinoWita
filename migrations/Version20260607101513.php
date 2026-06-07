<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260607101513 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE screening (id INT AUTO_INCREMENT NOT NULL, starts_at DATE NOT NULL, is_active TINYINT NOT NULL, movie_id INT NOT NULL, cinema_id INT NOT NULL, hall_id INT NOT NULL, INDEX IDX_B708297D8F93B6FC (movie_id), INDEX IDX_B708297DB4CB84B6 (cinema_id), INDEX IDX_B708297D52AFCFD6 (hall_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE screening ADD CONSTRAINT FK_B708297D8F93B6FC FOREIGN KEY (movie_id) REFERENCES movie (id)');
        $this->addSql('ALTER TABLE screening ADD CONSTRAINT FK_B708297DB4CB84B6 FOREIGN KEY (cinema_id) REFERENCES cinema (id)');
        $this->addSql('ALTER TABLE screening ADD CONSTRAINT FK_B708297D52AFCFD6 FOREIGN KEY (hall_id) REFERENCES cinema_hall (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE screening DROP FOREIGN KEY FK_B708297D8F93B6FC');
        $this->addSql('ALTER TABLE screening DROP FOREIGN KEY FK_B708297DB4CB84B6');
        $this->addSql('ALTER TABLE screening DROP FOREIGN KEY FK_B708297D52AFCFD6');
        $this->addSql('DROP TABLE screening');
    }
}
