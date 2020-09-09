<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

namespace execut\crudFields\models\AllFields;


use execut\crudFields\fields\Field;
use yii\db\ActiveRecord;

class Select2Via extends ActiveRecord
{
    public function rules()
    {
        return [
            [self::primaryKey(), 'required', 'on' => Field::SCENARIO_FORM],
        ];
    }

    public static function tableName()
    {
        return 'example_all_fields_has_many_select2';
    }

    public static function primaryKey()
    {
        return ['example_all_field_from_id', 'example_all_field_to_id'];
    }
}