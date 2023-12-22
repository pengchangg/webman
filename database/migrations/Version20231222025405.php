<?php

declare(strict_types=1);

namespace database\migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231222025405 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add';
    }

    private $indexName = 'test_uuid_unique';

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $table = $schema->getTable('test');
        $table->dropColumn('age1');
        $table->addColumn('age', 'integer')->setNotnull(true)->setDefault(10);

        $table->addColumn('uuid', 'string')->setLength(32)->setNotnull(true)->setOptions(['fixed'=>true]);
        $table->addUniqueIndex(['uuid'],$this->indexName);

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $table = $schema->getTable('test');
        $table->addColumn('age1', 'integer')->setNotnull(true)->setDefault(10);
        $table->dropIndex($this->indexName);
        $table->dropColumn('age');
        $table->dropColumn('uuid');

    }
}
