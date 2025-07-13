<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250531163154 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add many-to-many relationships for music, category, artist, and album entities';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
                CREATE TABLE music_category (music_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_C15F45E7399BBB13 (music_id), INDEX IDX_C15F45E712469DE2 (category_id), PRIMARY KEY(music_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
            SQL);
        $this->addSql(<<<'SQL'
                CREATE TABLE music_artist (music_id INT NOT NULL, artist_id INT NOT NULL, INDEX IDX_9481557E399BBB13 (music_id), INDEX IDX_9481557EB7970CF8 (artist_id), PRIMARY KEY(music_id, artist_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
            SQL);
        $this->addSql(<<<'SQL'
                CREATE TABLE music_album (music_id INT NOT NULL, album_id INT NOT NULL, INDEX IDX_5E11158B399BBB13 (music_id), INDEX IDX_5E11158B1137ABCF (album_id), PRIMARY KEY(music_id, album_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE music_category ADD CONSTRAINT FK_C15F45E7399BBB13 FOREIGN KEY (music_id) REFERENCES music (id) ON DELETE CASCADE
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE music_category ADD CONSTRAINT FK_C15F45E712469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE music_artist ADD CONSTRAINT FK_9481557E399BBB13 FOREIGN KEY (music_id) REFERENCES music (id) ON DELETE CASCADE
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE music_artist ADD CONSTRAINT FK_9481557EB7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id) ON DELETE CASCADE
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE music_album ADD CONSTRAINT FK_5E11158B399BBB13 FOREIGN KEY (music_id) REFERENCES music (id) ON DELETE CASCADE
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE music_album ADD CONSTRAINT FK_5E11158B1137ABCF FOREIGN KEY (album_id) REFERENCES album (id) ON DELETE CASCADE
            SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
                ALTER TABLE music_category DROP FOREIGN KEY FK_C15F45E7399BBB13
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE music_category DROP FOREIGN KEY FK_C15F45E712469DE2
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE music_artist DROP FOREIGN KEY FK_9481557E399BBB13
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE music_artist DROP FOREIGN KEY FK_9481557EB7970CF8
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE music_album DROP FOREIGN KEY FK_5E11158B399BBB13
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE music_album DROP FOREIGN KEY FK_5E11158B1137ABCF
            SQL);
        $this->addSql(<<<'SQL'
                DROP TABLE music_category
            SQL);
        $this->addSql(<<<'SQL'
                DROP TABLE music_artist
            SQL);
        $this->addSql(<<<'SQL'
                DROP TABLE music_album
            SQL);
    }
}
