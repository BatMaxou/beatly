<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250718202732 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Artist Request';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE artist_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, message LONGTEXT NOT NULL, status VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8CD8D1A8A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE artist_request ADD CONSTRAINT FK_8CD8D1A8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE music_file ADD artist_request_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE music_file ADD CONSTRAINT FK_D8AE328BA10167DC FOREIGN KEY (artist_request_id) REFERENCES artist_request (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D8AE328BA10167DC ON music_file (artist_request_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE music_file DROP FOREIGN KEY FK_D8AE328BA10167DC
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE artist_request DROP FOREIGN KEY FK_8CD8D1A8A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE artist_request
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_D8AE328BA10167DC ON music_file
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE music_file DROP artist_request_id
        SQL);
    }
}
