<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250710063617 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Make playlist/music many-to-many relation a many-to-many relation with additional fields';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
                ALTER TABLE playlist_music DROP FOREIGN KEY FK_6E4E3B096BBD148
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE playlist_music DROP FOREIGN KEY FK_6E4E3B09399BBB13
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE playlist_music ADD id INT AUTO_INCREMENT NOT NULL, ADD position INT NOT NULL, ADD added_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', DROP PRIMARY KEY, ADD PRIMARY KEY (id)
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE playlist_music ADD CONSTRAINT FK_6E4E3B096BBD148 FOREIGN KEY (playlist_id) REFERENCES playlist (id)
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE playlist_music ADD CONSTRAINT FK_6E4E3B09399BBB13 FOREIGN KEY (music_id) REFERENCES music (id)
            SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
                ALTER TABLE playlist_music MODIFY id INT NOT NULL
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE playlist_music DROP FOREIGN KEY FK_6E4E3B096BBD148
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE playlist_music DROP FOREIGN KEY FK_6E4E3B09399BBB13
            SQL);
        $this->addSql(<<<'SQL'
                DROP INDEX `PRIMARY` ON playlist_music
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE playlist_music DROP id, DROP position, DROP added_at
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE playlist_music ADD CONSTRAINT FK_6E4E3B096BBD148 FOREIGN KEY (playlist_id) REFERENCES playlist (id) ON DELETE CASCADE
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE playlist_music ADD CONSTRAINT FK_6E4E3B09399BBB13 FOREIGN KEY (music_id) REFERENCES music (id) ON DELETE CASCADE
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE playlist_music ADD PRIMARY KEY (playlist_id, music_id)
            SQL);
    }
}
