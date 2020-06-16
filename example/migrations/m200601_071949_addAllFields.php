<?php
namespace execut\crudFields\example\migrations;
use execut\yii\migration\Migration;
use execut\yii\migration\Inverter;

class m200601_071949_addAllFields extends Migration
{
    public function initInverter(Inverter $i)
    {
        $i->table('example_all_fields')
            ->create($this->defaultColumns([
                'bool' => $this->boolean(),
            ]))
            ->addForeignColumn('example_all_fields', false, null, 'has_one_id');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
