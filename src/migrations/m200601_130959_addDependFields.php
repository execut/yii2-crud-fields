<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\migrations;

use execut\yii\migration\Migration;
use execut\yii\migration\Inverter;

/**
 * Migration m200601_130959_addDependFields
 * @package execut\crudFields\migrations
 */
class m200601_130959_addDependFields extends Migration
{
    /**
     * {@inheritDoc}
     */
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
}
