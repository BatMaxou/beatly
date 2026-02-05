<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250707102656 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add listened_at column to last_listened table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
                ALTER TABLE last_listened ADD listened_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)'
            SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
                ALTER TABLE last_listened DROP listened_at
            SQL);
    }
}
