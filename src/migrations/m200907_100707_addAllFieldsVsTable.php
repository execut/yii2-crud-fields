<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\migrations;

/**
 * Class m200907_100707_addAllFieldsVsTable
 */
class m200907_100707_addAllFieldsVsTable extends \execut\yii\migration\Migration
{
    public function initInverter(\execut\yii\migration\Inverter $i)
    {
        $i->table('example_all_fields')
            ->addForeignColumn('example_all_fields', null, null, 'has_many_select2_id');

        $i->table('example_all_fields_has_many_select2')
            ->create($this->defaultColumns())
            ->addForeignColumn('example_all_fields', true, null, 'example_all_field_from_id')
            ->addForeignColumn('example_all_fields', true, null, 'example_all_field_to_id');

        $i->table('example_all_fields')
            ->addForeignColumn('example_all_fields', null, null, 'has_many_multipleinput_id');

        $i->table('example_all_fields_has_many_multipleinput')
            ->create($this->defaultColumns())
            ->addForeignColumn('example_all_fields', true, null, 'example_all_field_from_id')
            ->addForeignColumn('example_all_fields', true, null, 'example_all_field_to_id');
    }
}
