<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 2/14/19
 * Time: 1:05 PM
 */

namespace execut\crudFields\fields;


use unclead\multipleinput\MultipleInputColumn;
use yii\db\ActiveQuery;
use yii\widgets\ActiveForm;

class RelationsFilterField extends Field
{
    public $attribute = 'isHasRelation';
    public function getMultipleInputField() {
        return [
            'type' => MultipleInputColumn::TYPE_DROPDOWN,
            'name' => $this->attribute,
            'items' => [
                '' => '',
                '1' => 'Есть',
                '0' => 'Нет',
            ],
        ];
    }

    public function rules()
    {
        $rules = parent::rules(); // TODO: Change the autogenerated stub
        $rules[$this->attribute . 'SafeDefault'] = [[$this->attribute], 'safe'];

        return $rules;
    }

    public function getField()
    {
        return false;
    }

    public function getColumn()
    {
        return false;
    }

    public function applyScopes(ActiveQuery $query)
    {
        return parent::applyScopes($query);
    }
}