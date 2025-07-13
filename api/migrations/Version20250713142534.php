<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250713142534 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Introduce Queue';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
                CREATE TABLE queue (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_7FFD7F63A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
            SQL);
        $this->addSql(<<<'SQL'
                CREATE TABLE queue_item (id INT AUTO_INCREMENT NOT NULL, music_id INT NOT NULL, queue_id INT NOT NULL, position INT NOT NULL, INDEX IDX_BA4B6DE8399BBB13 (music_id), INDEX IDX_BA4B6DE8477B5BAE (queue_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE queue ADD CONSTRAINT FK_7FFD7F63A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE queue_item ADD CONSTRAINT FK_BA4B6DE8399BBB13 FOREIGN KEY (music_id) REFERENCES music (id)
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE queue_item ADD CONSTRAINT FK_BA4B6DE8477B5BAE FOREIGN KEY (queue_id) REFERENCES queue (id)
            SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
                ALTER TABLE queue DROP FOREIGN KEY FK_7FFD7F63A76ED395
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE queue_item DROP FOREIGN KEY FK_BA4B6DE8399BBB13
            SQL);
        $this->addSql(<<<'SQL'
                ALTER TABLE queue_item DROP FOREIGN KEY FK_BA4B6DE8477B5BAE
            SQL);
        $this->addSql(<<<'SQL'
                DROP TABLE queue
            SQL);
        $this->addSql(<<<'SQL'
                DROP TABLE queue_item
            SQL);
    }
}
