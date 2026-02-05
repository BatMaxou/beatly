<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250710093223 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Make album/music many-to-many relation a many-to-many relation with additional fields';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
                CREATE TABLE album_music (id INT AUTO_INCREMENT NOT NULL, album_id INT NOT NULL, music_id INT NOT NULL, position INT NOT NULL, added_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_BBEA686C1137ABCF (album_id), INDEX IDX_BBEA686C399BBB13 (music_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE album_music ADD CONSTRAINT FK_BBEA686C1137ABCF FOREIGN KEY (album_id) REFERENCES album (id)
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE album_music ADD CONSTRAINT FK_BBEA686C399BBB13 FOREIGN KEY (music_id) REFERENCES music (id)
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE music_album DROP FOREIGN KEY FK_5E11158B399BBB13
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE music_album DROP FOREIGN KEY FK_5E11158B1137ABCF
            SQL);
        $this->addSql(<<<'SQL'
                DROP TABLE music_album
            SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
                CREATE TABLE music_album (music_id INT NOT NULL, album_id INT NOT NULL, INDEX IDX_5E11158B399BBB13 (music_id), INDEX IDX_5E11158B1137ABCF (album_id), PRIMARY KEY(music_id, album_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE music_album ADD CONSTRAINT FK_5E11158B399BBB13 FOREIGN KEY (music_id) REFERENCES music (id) ON DELETE CASCADE
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE music_album ADD CONSTRAINT FK_5E11158B1137ABCF FOREIGN KEY (album_id) REFERENCES album (id) ON DELETE CASCADE
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE album_music DROP FOREIGN KEY FK_BBEA686C1137ABCF
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE album_music DROP FOREIGN KEY FK_BBEA686C399BBB13
            SQL);
        $this->addSql(<<<'SQL'
                DROP TABLE album_music
            SQL);
    }
}
