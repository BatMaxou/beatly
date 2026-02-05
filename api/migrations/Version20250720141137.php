<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250720141137 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add music_id to Music File';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE music_file ADD music_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE music_file ADD CONSTRAINT FK_D8AE328B399BBB13 FOREIGN KEY (music_id) REFERENCES music (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_D8AE328B399BBB13 ON music_file (music_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE music_file DROP FOREIGN KEY FK_D8AE328B399BBB13
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_D8AE328B399BBB13 ON music_file
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE music_file DROP music_id
        SQL);
    }
}
