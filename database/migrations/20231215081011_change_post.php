<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class ChangePost extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $table = $this->table("post");
        $table
            ->addColumn('add_column', 'string', ['limit' => 100, 'null' => false])
            ->update();

        if ($this->isMigratingUp()) {
            $table->insert([
                    ['title' => 'title1', 'content' => 'content1', 'add_column' => 'add_column1'],
                    ['title' => 'title2', 'content' => 'content2', 'add_column' => 'add_column2'],
                    ['title' => 'title3', 'content' => 'content3', 'add_column' => 'add_column3'],
                ])
                ->save();
        }
    }
}
