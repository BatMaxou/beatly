<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250629195146 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'remove duration from music entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
                ALTER TABLE music DROP duration;
            SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
                ALTER TABLE music ADD duration INT NOT NULL;
            SQL);
    }
}
