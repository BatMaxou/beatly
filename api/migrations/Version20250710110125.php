<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250710110125 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove position column from playlist_music table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
                ALTER TABLE playlist_music DROP position
            SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
                ALTER TABLE playlist_music ADD position INT NOT NULL
            SQL);
    }
}
