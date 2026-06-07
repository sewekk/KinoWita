<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260607115116 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fix movie category column and screening starts_at datetime type';
    }

    public function up(Schema $schema): void
    {
        if (!$schema->getTable('movie')->hasColumn('category')) {
            $this->addSql("ALTER TABLE movie ADD category VARCHAR(255) DEFAULT 'inne' NOT NULL");
        } else {
            $this->addSql("ALTER TABLE movie CHANGE category category VARCHAR(255) DEFAULT 'inne' NOT NULL");
        }

        $this->addSql('ALTER TABLE screening CHANGE starts_at starts_at DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        if ($schema->getTable('movie')->hasColumn('category')) {
            $this->addSql("ALTER TABLE movie CHANGE category category VARCHAR(50) DEFAULT 'inne' NOT NULL");
        }

        $this->addSql('ALTER TABLE screening CHANGE starts_at starts_at DATE NOT NULL');
    }
}