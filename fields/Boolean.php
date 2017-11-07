<?php
/**
 */

namespace execut\crudFields\fields;


use kartik\detail\DetailView;
use kartik\grid\BooleanColumn;
use unclead\multipleinput\MultipleInputColumn;
use yii\helpers\ArrayHelper;

class Boolean extends Field
{
    public $multipleInputType = MultipleInputColumn::TYPE_CHECKBOX;
    public function getColumn()
    {
        return ArrayHelper::merge([
            'class' => BooleanColumn::class,
        ], parent::getColumn());
    }

    public function getField()
    {
        $field = parent::getField();
        if ($field === false) {
            return false;
        }

        if ($this->getDisplayOnly()) {
            $value = function () {
                if ($this->getValue()) {
                    return 'Да';
                } else {
                    return 'Нет';
                }
            };
        } else {
            $value = null;
        }

        return array_merge($field, [
            'type' => DetailView::INPUT_CHECKBOX,
            'value' => $value,
        ]);
    }
}