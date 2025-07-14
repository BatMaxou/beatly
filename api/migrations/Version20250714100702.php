<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250714100702 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add UUID to embeddable entities: album, artist, music, playlist';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
                ALTER TABLE album ADD uuid VARCHAR(255) NOT NULL
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE artist ADD uuid VARCHAR(255) NOT NULL
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE music ADD uuid VARCHAR(255) NOT NULL
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE playlist ADD uuid VARCHAR(255) NOT NULL
            SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
                ALTER TABLE album DROP uuid
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE artist DROP uuid
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE music DROP uuid
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE playlist DROP uuid
            SQL);
    }
}
