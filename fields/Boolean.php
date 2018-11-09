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
        $column = parent::getColumn();
        if ($column === false) {
            return false;
        }

        return ArrayHelper::merge([
            'class' => BooleanColumn::class,
        ], $column);
    }

    public function getField()
    {
        $field = parent::getField();
        if ($field === false) {
            return false;
        }

        if ($this->getDisplayOnly()) {
            $value = function () {
                return $this->getValueString();
            };
        } else {
            $value = null;
        }

        $result = [
            'type' => DetailView::INPUT_CHECKBOX,
        ];

        if ($value !== null) {
            $result['value'] = $value;
        }

        return array_merge($field, $result);
    }

    protected function getValueString() {
        if ($this->getValue()) {
            return 'Да';
        } else if (!$this->getValue() && $this->getValue() !== '') {
            return 'Нет';
        }
    }
}