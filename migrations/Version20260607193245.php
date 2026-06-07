<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260607193245 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, status TINYINT NOT NULL, created_at DATETIME NOT NULL, screening_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_42C8495570F5295D (screening_id), INDEX IDX_42C84955A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE reservation_seat (id INT AUTO_INCREMENT NOT NULL, `row_number` INT NOT NULL, seat_number INT NOT NULL, reservation_id INT NOT NULL, screening_id INT NOT NULL, INDEX IDX_2B65FB0EB83297E7 (reservation_id), INDEX IDX_2B65FB0E70F5295D (screening_id), UNIQUE INDEX uniq_screening_seat (screening_id, `row_number`, seat_number), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495570F5295D FOREIGN KEY (screening_id) REFERENCES screening (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reservation_seat ADD CONSTRAINT FK_2B65FB0EB83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id)');
        $this->addSql('ALTER TABLE reservation_seat ADD CONSTRAINT FK_2B65FB0E70F5295D FOREIGN KEY (screening_id) REFERENCES screening (id)');
        $this->addSql('ALTER TABLE movie CHANGE category category VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495570F5295D');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955A76ED395');
        $this->addSql('ALTER TABLE reservation_seat DROP FOREIGN KEY FK_2B65FB0EB83297E7');
        $this->addSql('ALTER TABLE reservation_seat DROP FOREIGN KEY FK_2B65FB0E70F5295D');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE reservation_seat');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE movie CHANGE category category VARCHAR(255) DEFAULT \'inne\' NOT NULL');
    }
}
