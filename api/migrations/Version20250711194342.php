<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250711194342 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add main artist to music';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE music ADD main_artist_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE music ADD CONSTRAINT FK_CD52224A9721AB5A FOREIGN KEY (main_artist_id) REFERENCES artist (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_CD52224A9721AB5A ON music (main_artist_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE music DROP FOREIGN KEY FK_CD52224A9721AB5A
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_CD52224A9721AB5A ON music
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE music DROP main_artist_id
        SQL);
    }
}
