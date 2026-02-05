<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250621132557 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add playlist_music table to manage many-to-many relationship between playlists and music';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
                CREATE TABLE playlist_music (playlist_id INT NOT NULL, music_id INT NOT NULL, INDEX IDX_6E4E3B096BBD148 (playlist_id), INDEX IDX_6E4E3B09399BBB13 (music_id), PRIMARY KEY(playlist_id, music_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE playlist_music ADD CONSTRAINT FK_6E4E3B096BBD148 FOREIGN KEY (playlist_id) REFERENCES playlist (id) ON DELETE CASCADE
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE playlist_music ADD CONSTRAINT FK_6E4E3B09399BBB13 FOREIGN KEY (music_id) REFERENCES music (id) ON DELETE CASCADE
            SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
                ALTER TABLE playlist_music DROP FOREIGN KEY FK_6E4E3B096BBD148
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE playlist_music DROP FOREIGN KEY FK_6E4E3B09399BBB13
            SQL);
        $this->addSql(<<<'SQL'
                DROP TABLE playlist_music
            SQL);
    }
}
