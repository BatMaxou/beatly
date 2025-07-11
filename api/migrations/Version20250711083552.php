<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250711083552 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change cover_name to nullable';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE music CHANGE cover_name cover_name VARCHAR(255) DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE music CHANGE cover_name cover_name VARCHAR(255) NOT NULL
        SQL);
    }
}
