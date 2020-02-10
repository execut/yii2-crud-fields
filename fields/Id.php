<?php
/**
 */

namespace execut\crudFields\fields;

use kartik\daterange\DateRangePicker;
use kartik\detail\DetailView;
use unclead\multipleinput\MultipleInputColumn;
use yii\helpers\ArrayHelper;

class Id extends Field
{
    public $attribute = 'id';
    public $multipleInputType = MultipleInputColumn::TYPE_HIDDEN_INPUT;
    public $displayOnly = true;

    public function getField()
    {
        if (!$this->getValue()) {
            return false;
        }

        return parent::getField();
    }

    public function rules()
    {
        $rules = $this->rules;
        if ($this->attribute !== null) {
            $rules[] = [
                [$this->attribute],
                'safe',
                'on' => self::SCENARIO_GRID,
            ];
        }

        $rules[$this->attribute . '_filter'] = [
            $this->attribute,
            'filter',
            'filter' => function ($v) {
            if (is_string($v)) {
                $column = $this->model->getTableSchema()->getColumn($this->attribute);
                if ($column) {
                    return $column->phpTypecast($v);
                }
            }

                return $v;
            },
            'on' => self::SCENARIO_GRID
        ];

        return $rules;
    }

    public function getMultipleInputField()
    {
        return false;
    }
}