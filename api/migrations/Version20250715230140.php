<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250715230140 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Refactor album / music relation';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE album_music DROP FOREIGN KEY FK_BBEA686C1137ABCF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE album_music DROP FOREIGN KEY FK_BBEA686C399BBB13
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE album_music
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE music ADD album_id INT DEFAULT NULL, ADD album_position INT NOT NULL, ADD added_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE music ADD CONSTRAINT FK_CD52224A1137ABCF FOREIGN KEY (album_id) REFERENCES album (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_CD52224A1137ABCF ON music (album_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE album_music (id INT AUTO_INCREMENT NOT NULL, album_id INT NOT NULL, music_id INT NOT NULL, position INT NOT NULL, added_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_BBEA686C399BBB13 (music_id), INDEX IDX_BBEA686C1137ABCF (album_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE album_music ADD CONSTRAINT FK_BBEA686C1137ABCF FOREIGN KEY (album_id) REFERENCES album (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE album_music ADD CONSTRAINT FK_BBEA686C399BBB13 FOREIGN KEY (music_id) REFERENCES music (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE music DROP FOREIGN KEY FK_CD52224A1137ABCF
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_CD52224A1137ABCF ON music
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE music DROP album_id, DROP album_position, DROP added_at
        SQL);
    }
}
