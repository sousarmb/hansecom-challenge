<?php

declare(strict_types=1);

namespace Quotes\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250327105247 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create quote requests log table';
    }

    public function up(Schema $schema): void
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `quote_requests` ( 
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `owner` VARCHAR(256) NOT NULL,
    `datetime_request` DATETIME NOT NULL,
    `quote` TEXT NOT NULL, 
    `symbol` VARCHAR(6) NOT NULL,
    INDEX idx_uuid (`owner`),
    INDEX idx_symbol (`symbol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;
        $this->addSql($sql);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE `quote_requests`;');
    }
}
