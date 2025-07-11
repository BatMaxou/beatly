<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250711083148 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add artist_id to album';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE album ADD artist_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE album ADD CONSTRAINT FK_39986E43B7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_39986E43B7970CF8 ON album (artist_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE album DROP FOREIGN KEY FK_39986E43B7970CF8
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_39986E43B7970CF8 ON album
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE album DROP artist_id
        SQL);
    }
}
