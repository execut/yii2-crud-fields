<?php
/**
 */

namespace execut\crudFields\fields;


use yii\base\Object;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

class Field extends Object
{
    const SCENARIO_GRID = 'grid';
    const SCENARIO_FORM = 'form';
    public $model = null;
    public $required = false;
    public $attribute = null;
    protected $_column = [];
    protected $_field = [];
    public function getColumn() {
        $column = $this->_column;
        if ($this->attribute !== null) {
            $column['attribute'] = $this->attribute;
        }

        return $column;
    }

    public function setColumn($column) {
        $this->_column = $column;

        return $this;
    }

    public function getField() {
        $field = $this->_field;
        if ($this->attribute !== null) {
            $field['attribute'] = $this->attribute;
        }

        return $field;
    }

    public function setField($field) {
        $this->_field = $field;

        return $this;
    }

    public function applyScopes(ActiveQuery $query) {
        $attribute = $this->attribute;
        if ($attribute !== null) {
            if ($this->model->$attribute !== null && $this->model->$attribute !== '') {
                $query->andWhere([
                    $attribute => $this->model->$attribute,
                ]);
            }
        }

        return $query;
    }

    public function rules() {
        $rules = [];
        if ($this->attribute !== null) {
            $rules[] = [
                [$this->attribute],
                'safe',
                'on' => self::SCENARIO_GRID,
            ];

            if ($this->required) {
                $rule = 'required';
            } else {
                $rule = 'safe';
            }
            $rules[] = [
                [$this->attribute],
                $rule,
                'on' => self::SCENARIO_FORM,
            ];
        }

        return $rules;
    }
}