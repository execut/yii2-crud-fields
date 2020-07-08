<?php
namespace execut\crudFields\example\migrations;

use execut\yii\migration\Migration;
use execut\yii\migration\Inverter;

class m200708_105337_addSimpleTable extends Migration
{
    public function initInverter(Inverter $i)
    {
        $i->table('example_simple')
            ->create($this->defaultColumns(['name' => $this->string()->notNull()]));
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

