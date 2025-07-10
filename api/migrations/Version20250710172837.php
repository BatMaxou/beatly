<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250710172837 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add cover_name and wallpaper_name to album, remove old cover and wallpaper fields';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE album ADD cover_name VARCHAR(255) DEFAULT NULL, ADD wallpaper_name VARCHAR(255) DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', DROP cover, DROP wallpaper
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE album ADD cover VARCHAR(255) DEFAULT NULL, ADD wallpaper VARCHAR(255) DEFAULT NULL, DROP cover_name, DROP wallpaper_name, DROP updated_at
        SQL);
    }
}
