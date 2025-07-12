<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250629074230 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add uploadable cover for music entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
                ALTER TABLE music ADD cover_name VARCHAR(255) NOT NULL, ADD updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', DROP cover
            SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
                ALTER TABLE music ADD cover VARCHAR(255) DEFAULT NULL, DROP cover_name, DROP updated_at
            SQL);
    }
}
