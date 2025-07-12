<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250625205221 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add Vich Uploader fields to music_file entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
                ALTER TABLE music_file ADD updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', CHANGE file name VARCHAR(255) NOT NULL
            SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
                ALTER TABLE music_file DROP updated_at, CHANGE name file VARCHAR(255) NOT NULL
            SQL);
    }
}
