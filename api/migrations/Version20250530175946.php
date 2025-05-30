<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250530175946 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial migration without Many to Many relations';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE album (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, cover VARCHAR(255) DEFAULT NULL, wallpaper VARCHAR(255) DEFAULT NULL, release_date DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE artist (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, color VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE favorite (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, music_id INT NOT NULL, INDEX IDX_68C58ED9A76ED395 (user_id), INDEX IDX_68C58ED9399BBB13 (music_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE last_album_listened (id INT NOT NULL, album_id INT NOT NULL, INDEX IDX_FCA32EE21137ABCF (album_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE last_listened (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, discr VARCHAR(255) NOT NULL, INDEX IDX_1929C4EDA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE last_music_listened (id INT NOT NULL, music_id INT NOT NULL, INDEX IDX_C85CD4D1399BBB13 (music_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE last_playlist_listened (id INT NOT NULL, playlist_id INT NOT NULL, INDEX IDX_350FDAC66BBD148 (playlist_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE music (id INT AUTO_INCREMENT NOT NULL, file_id INT NOT NULL, title VARCHAR(255) NOT NULL, duration INT NOT NULL, cover VARCHAR(255) DEFAULT NULL, listenings_number INT NOT NULL, UNIQUE INDEX UNIQ_CD52224A93CB796C (file_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE music_file (id INT AUTO_INCREMENT NOT NULL, file VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE platform (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE platform_playlist (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE playlist (id INT AUTO_INCREMENT NOT NULL, creator_id INT NOT NULL, title VARCHAR(255) NOT NULL, cover VARCHAR(255) DEFAULT NULL, wallpaper VARCHAR(255) DEFAULT NULL, discr VARCHAR(255) NOT NULL, INDEX IDX_D782112D61220EA6 (creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, avatar VARCHAR(255) DEFAULT NULL, wallpaper VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles LONGTEXT NOT NULL COMMENT '(DC2Type:json)', reset_token VARCHAR(255) DEFAULT NULL, discr VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE artist ADD CONSTRAINT FK_1599687BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE favorite ADD CONSTRAINT FK_68C58ED9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE favorite ADD CONSTRAINT FK_68C58ED9399BBB13 FOREIGN KEY (music_id) REFERENCES music (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE last_album_listened ADD CONSTRAINT FK_FCA32EE21137ABCF FOREIGN KEY (album_id) REFERENCES album (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE last_album_listened ADD CONSTRAINT FK_FCA32EE2BF396750 FOREIGN KEY (id) REFERENCES last_listened (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE last_listened ADD CONSTRAINT FK_1929C4EDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE last_music_listened ADD CONSTRAINT FK_C85CD4D1399BBB13 FOREIGN KEY (music_id) REFERENCES music (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE last_music_listened ADD CONSTRAINT FK_C85CD4D1BF396750 FOREIGN KEY (id) REFERENCES last_listened (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE last_playlist_listened ADD CONSTRAINT FK_350FDAC66BBD148 FOREIGN KEY (playlist_id) REFERENCES playlist (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE last_playlist_listened ADD CONSTRAINT FK_350FDAC6BF396750 FOREIGN KEY (id) REFERENCES last_listened (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE music ADD CONSTRAINT FK_CD52224A93CB796C FOREIGN KEY (file_id) REFERENCES music_file (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE platform ADD CONSTRAINT FK_3952D0CBBF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE platform_playlist ADD CONSTRAINT FK_52F2113FBF396750 FOREIGN KEY (id) REFERENCES playlist (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE playlist ADD CONSTRAINT FK_D782112D61220EA6 FOREIGN KEY (creator_id) REFERENCES user (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE artist DROP FOREIGN KEY FK_1599687BF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE favorite DROP FOREIGN KEY FK_68C58ED9A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE favorite DROP FOREIGN KEY FK_68C58ED9399BBB13
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE last_album_listened DROP FOREIGN KEY FK_FCA32EE21137ABCF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE last_album_listened DROP FOREIGN KEY FK_FCA32EE2BF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE last_listened DROP FOREIGN KEY FK_1929C4EDA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE last_music_listened DROP FOREIGN KEY FK_C85CD4D1399BBB13
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE last_music_listened DROP FOREIGN KEY FK_C85CD4D1BF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE last_playlist_listened DROP FOREIGN KEY FK_350FDAC66BBD148
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE last_playlist_listened DROP FOREIGN KEY FK_350FDAC6BF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE music DROP FOREIGN KEY FK_CD52224A93CB796C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE platform DROP FOREIGN KEY FK_3952D0CBBF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE platform_playlist DROP FOREIGN KEY FK_52F2113FBF396750
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE playlist DROP FOREIGN KEY FK_D782112D61220EA6
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE album
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE artist
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE category
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE favorite
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE last_album_listened
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE last_listened
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE last_music_listened
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE last_playlist_listened
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE music
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE music_file
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE platform
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE platform_playlist
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE playlist
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user
        SQL);
    }
}
