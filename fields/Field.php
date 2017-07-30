<?php
/**
 */

namespace execut\crudFields\fields;


use execut\crudFields\Relation;
use yii\base\Object;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\helpers\Inflector;

class Field extends Object
{
    const SCENARIO_GRID = 'grid';
    const SCENARIO_FORM = 'form';
    public $module = null;
    public $model = null;
    public $required = false;
    public $attribute = null;
    public $rules = [];
    protected $_column = [];
    protected $_field = [];

    public $nameAttribute = 'name';
    public $with = null;
    public $relation = null;
    public $data = [];
    public $valueAttribute = null;
    public $scope = null;

    protected $_relationObject = null;
    public $order = 0;

    public function setRelationObject($object) {
        $this->_relationObject = $object;

        return $this;
    }

    public function getRelationObject() {
        if ($this->_relationObject === null) {
            $this->_relationObject = new Relation([
                'field' => $this,
                'name' => $this->relation,
                'nameAttribute' => $this->nameAttribute,
                'with' => $this->with,
                'valueAttribute' => $this->valueAttribute,
            ]);
        }

        return $this->_relationObject;
    }

    public function getValue() {
        $attribute = $this->attribute;

        return $this->model->$attribute;
    }

    public function getData() {
        if (empty($this->data)) {
            return $this->getRelationObject()->getData();
        }

        return $this->data;
    }

    public function getColumn() {
        $column = $this->_column;
        if (is_callable($column)) {
            $column = $column();
        }

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
        if ($field === false) {
            return false;
        }

        if (is_callable($field)) {
            $field = $field($this->model, $this);
        }

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
        $scopeResult = true;
        if ($this->scope !== null) {
            $scope = $this->scope;
            $scopeResult = $scope($query, $this->model);
        }

        if ($scopeResult && $this->attribute) {
            $value = $this->getValue();
            if (!empty($value)) {
                $query->andFilterWhere([
                    $attribute => $value,
                ]);
            }
        }

        $this->applyRelationScopes($query);

        return $query;
    }

    public function rules() {
        $rules = $this->rules;
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

    public function getLabel() {
        $attribute = Inflector::humanize($this->attribute, '_');

        return \Yii::t('modules/' . $this->module . '/', $attribute);
    }

    /**
     * @param ActiveQuery $query
     */
    protected function applyRelationScopes(ActiveQuery $query): void
    {
        if ($this->relation) {
            $this->getRelationObject()->applyScopes($query);
        }
    }
}