<?php

declare(strict_types=1);

namespace database\migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231222023007 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'test xxxxxxxxxxxxxx';
  }

  public function up(Schema $schema): void
  {
    // this up() migration is auto-generated, please modify it to your needs
    $table = $schema->createTable("test");
    $table->addColumn('name', 'string');
    $table->addColumn('id', 'integer')->setAutoincrement(true);
    $table->setPrimaryKey(['id']);
  }

  public function down(Schema $schema): void
  {
    // this down() migration is auto-generated, please modify it to your needs
    if ($schema->hasTable('test')) {
      $schema->dropTable('test');
    }
  }
}
