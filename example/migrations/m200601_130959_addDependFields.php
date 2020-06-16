<?php
namespace execut\crudFields\example\migrations;

use execut\yii\migration\Migration;
use execut\yii\migration\Inverter;

class m200601_130959_addDependFields extends Migration
{
    public function initInverter(Inverter $i)
    {
        $i->table('example_all_fields')
            ->addColumns([
                'name' => $this->string()->notNull(),
                'periodically_updated' => $this->dateTime(),
                'record_for_update_when_a_specific_value_selected' => $this->boolean(),
                'change_updated' => $this->dateTime(),
                'empty_updated' => $this->dateTime(),
                'not_empty_updated' => $this->dateTime(),
                'specific_value_selected_updated' => $this->dateTime(),
            ])
            ->addForeignColumn('example_all_fields', false, null, 'periodically_updated_widget_id')
        ;
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
