<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250714211049 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Introduce Favorites';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
                CREATE TABLE favorite_album (id INT NOT NULL, album_id INT NOT NULL, INDEX IDX_8F1045671137ABCF (album_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
            SQL);
        $this->addSql(<<<'SQL'
                CREATE TABLE favorite_music (id INT NOT NULL, music_id INT NOT NULL, INDEX IDX_7BDA096E399BBB13 (music_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
            SQL);
        $this->addSql(<<<'SQL'
                CREATE TABLE favorite_playlist (id INT NOT NULL, playlist_id INT NOT NULL, INDEX IDX_7DD509DC6BBD148 (playlist_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE favorite_album ADD CONSTRAINT FK_8F1045671137ABCF FOREIGN KEY (album_id) REFERENCES album (id)
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE favorite_album ADD CONSTRAINT FK_8F104567BF396750 FOREIGN KEY (id) REFERENCES favorite (id) ON DELETE CASCADE
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE favorite_music ADD CONSTRAINT FK_7BDA096E399BBB13 FOREIGN KEY (music_id) REFERENCES music (id)
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE favorite_music ADD CONSTRAINT FK_7BDA096EBF396750 FOREIGN KEY (id) REFERENCES favorite (id) ON DELETE CASCADE
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE favorite_playlist ADD CONSTRAINT FK_7DD509DC6BBD148 FOREIGN KEY (playlist_id) REFERENCES playlist (id)
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE favorite_playlist ADD CONSTRAINT FK_7DD509DCBF396750 FOREIGN KEY (id) REFERENCES favorite (id) ON DELETE CASCADE
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE favorite DROP FOREIGN KEY FK_68C58ED9399BBB13
            SQL);
        $this->addSql(<<<'SQL'
                DROP INDEX IDX_68C58ED9399BBB13 ON favorite
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE favorite ADD added_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', ADD discr VARCHAR(255) NOT NULL, DROP music_id
            SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
                ALTER TABLE favorite_album DROP FOREIGN KEY FK_8F1045671137ABCF
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE favorite_album DROP FOREIGN KEY FK_8F104567BF396750
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE favorite_music DROP FOREIGN KEY FK_7BDA096E399BBB13
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE favorite_music DROP FOREIGN KEY FK_7BDA096EBF396750
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE favorite_playlist DROP FOREIGN KEY FK_7DD509DC6BBD148
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE favorite_playlist DROP FOREIGN KEY FK_7DD509DCBF396750
            SQL);
        $this->addSql(<<<'SQL'
                DROP TABLE favorite_album
            SQL);
        $this->addSql(<<<'SQL'
                DROP TABLE favorite_music
            SQL);
        $this->addSql(<<<'SQL'
                DROP TABLE favorite_playlist
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE favorite ADD music_id INT NOT NULL, DROP added_at, DROP discr
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE favorite ADD CONSTRAINT FK_68C58ED9399BBB13 FOREIGN KEY (music_id) REFERENCES music (id)
            SQL);
        $this->addSql(<<<'SQL'
                CREATE INDEX IDX_68C58ED9399BBB13 ON favorite (music_id)
            SQL);
    }
}
