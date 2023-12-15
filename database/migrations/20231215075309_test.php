<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Test extends AbstractMigration
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
        $table=$this->table('test');
        $table->addColumn('user_id','integer')
            ->addColumn('name','string')
            ->addColumn('age','integer')
            ->create();

        if ($this->isMigratingUp()){
            $table->insert([
                ['user_id'=>1,'name'=>'张三','age'=>18],
                ['user_id'=>2,'name'=>'李四','age'=>19],
                ['user_id'=>3,'name'=>'王五','age'=>20],
            ])->save();
        }
    }
}
