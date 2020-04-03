<?php
/**
 */

namespace execut\crudFields\fields;


use execut\crudFields\Relation;
use execut\crudFields\widgets\HasRelationDropdown;
use unclead\multipleinput\MultipleInputColumn;
use yii\base\BaseObject;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Connection;
use yii\db\pgsql\Schema;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Inflector;

class Field extends BaseObject
{
    const SCENARIO_GRID = 'grid';
    const SCENARIO_FORM = 'form';
    const IS_RECORDS_VALUES = [
        self::IS_NOT_HAS_RECORDS,
        self::IS_HAS_RECORDS,
    ];
    const IS_HAS_RECORDS = 'isHasRecords';
    const IS_NOT_HAS_RECORDS = 'isNotHasRecords';
    public $module = null;
    public $isHasRelationAttribute = false;
    /**
     * @var ActiveRecord
     */
    public $model = null;
    public $required = false;
    public $defaultValue = null;
    public $attribute = null;
    public $rules = [];
    public $multipleInputType = MultipleInputColumn::TYPE_TEXT_INPUT;
    protected $_column = [];
    protected $_field = [];
    protected $_label = null;
    protected $displayOnly = false;
    protected $readOnly = null;
    protected $detailViewFieldClass = DetailViewField::class;
    public $isRenderRelationFields = false;
    public $isRenderInRelationForm = true;

    public $idAttribute = null;
    public $nameAttribute = 'name';
    public $orderByAttribute = null;
    public $with = null;
    public $relation = null;
    public $data = [];
    public $valueAttribute = null;
    public $multipleInputField = [];
    public $url = null;
    public $updateUrl = null;
    public $isNoRenderRelationLink = false;
    public $defaultScenario = self::SCENARIO_DEFAULT;
    public $name = null;

    /**
     * @var \Closure|null
     */
    public $scope = null;

    protected $_relationObject = null;
    public $order = 0;
    public $columnRecordsLimit = null;
    public $groupByVia = null;

    const SCENARIO_DEFAULT = [self::SCENARIO_FORM];

    public function getReadOnly() {
        if ($this->readOnly === null) {
            return $this->displayOnly;
        }

        return $this->readOnly;
    }

    public function attach() {
        if ($this->defaultValue !== null && in_array($this->model->scenario, $this->defaultScenario)) {
            $attribute = $this->attribute;
            if ($this->model->$attribute === null || $this->model->$attribute === []) {
                $defaultValue = $this->defaultValue;
                if (is_callable($defaultValue)) {
                    $defaultValue = $defaultValue();
                }

                $this->model->$attribute = $defaultValue;
            }
        }
    }

    public function setRelationObject($object) {
        $this->_relationObject = $object;

        return $this;
    }

    public $urlMaker = null;
    public function getRelationObject() {
        if ($this->_relationObject === null && $this->relation !== null) {
            $this->_relationObject = new Relation([
                'field' => $this,
                'name' => $this->relation,
                'nameAttribute' => $this->nameAttribute,
                'orderByAttribute' => $this->orderByAttribute,
                'with' => $this->with,
                'valueAttribute' => $this->valueAttribute,
                'updateUrl' => $this->updateUrl,
                'url' => $this->url,
                'attribute' => $this->attribute,
                'model' => $this->model,
                'columnRecordsLimit' => $this->columnRecordsLimit,
                'isHasRelationAttribute' => $this->isHasRelationAttribute,
                'isNoRenderRelationLink' => $this->isNoRenderRelationLink,
                'label' => $this->getLabel(),
                'idAttribute' => $this->idAttribute,
                'urlMaker' => $this->urlMaker,
            ]);
        }

        return $this->_relationObject;
    }

    public function getValue() {
        $attribute = $this->attribute;

        return $this->model->$attribute;
    }

//    public function isCheckRecordsValue($value = null) {
//        if ($value === null) {
//            $value = $this->getValue();
//        }
//
//        if (!is_array($value)) {
//            $value = [$value];
//        }
//
//        foreach (self::IS_RECORDS_VALUES as $excludedKey) {
//            if (in_array($excludedKey, $value)) {
//                return true;
//            }
//        }
//
//        return false;
//    }

    public function getData() {
        if (empty($this->data)) {
            $relationObject = $this->getRelationObject();
            if (!$relationObject) {
                /** @TODO Hack for mysql */
//            throw new Exception('Data is required or set relation name');
                return [];
            }

            return $relationObject->getData();
        }

        if (is_callable($this->data)) {
            $data = $this->data;
            return $data($this->model);
        }

        return $this->data;
    }

    public function getColumn() {
        $column = $this->_column;
        if ($column === false) {
            return false;
        }

        if (is_callable($column)) {
            $column = $column();
        }

        if ($this->attribute !== null) {
            if (empty($column['attribute'])) {
                $column['attribute'] = $this->attribute;
            }

            if (empty($column['label'])) {
                $column['label'] = $this->getLabel();
            }
        }

        return $column;
    }

    public function setColumn($column) {
        $this->_column = $column;

        return $this;
    }

    public function getField() {
        return $this->getDetailViewField()->getConfig($this->model);
    }


    protected $detailViewField = null;
    public function setDetailViewField($detailViewField) {
        $this->detailViewField = $detailViewField;
        return $this;
    }

    /**
     * @return DetailViewField|null
     */
    public function getDetailViewField() {
        if ($this->detailViewField === null) {
            $fieldConfig = $this->_field;
            if (is_callable($fieldConfig)) {
                $fieldConfig = function ($model, $detailViewField) use ($fieldConfig) {
                    return $fieldConfig($model, $this, $detailViewField);
                };
            }

            $this->detailViewField = new $this->detailViewFieldClass($fieldConfig, $this->attribute, $this->getDisplayOnly());
        }

        return $this->detailViewField;
    }

    public function setFieldConfig($config) {
        $this->_field = $config;

        return $this;
    }

    public function getFieldConfig() {
        return $this->_field;
    }

    public function setDisplayOnly($displayOnly) {
        $this->displayOnly = $displayOnly;
        return $this;
    }

    public function setReadOnly($readOnly) {
        $this->readOnly = $readOnly;
        return $this;
    }

    public function getDisplayOnly() {
        if ($this->displayOnly) {
            if (is_callable($this->displayOnly)) {
                return call_user_func($this->displayOnly);
            }
        }

        return $this->displayOnly;
    }

    public function getFields($isWithRelationsFields = true) {
        $fields = [];
        if ($this->getIsRenderRelationFields() && $isWithRelationsFields) {
            $relationObject = $this->getRelationObject();
            $relationFields = $relationObject->getRelationFields();
            foreach ($relationFields as $field) {
                $formFields = $field->getFields();
                foreach ($formFields as &$formField) {
                    if (empty($formField['valueColOptions'])) {
                        $formField['valueColOptions'] = [];
                    }

                    Html::addCssClass($formField['valueColOptions'], 'related-' . $relationObject->getName());
//                    if (!empty($formField['attribute'])) {
//                        ArrayHelper::setValue($formField, 'options.name', $this->model->formName() . '[' . $relationObject->getName() . '][' . $formField['attribute'] . ']');
//                    }
                }

                $fields = ArrayHelper::merge($fields, $formFields);
            }
        } else {
            $field = $this->getField();
            if ($field !== false) {
                $fieldKey = $this->getName();
                if (!$fieldKey) {
                    $fieldKey = 0;
                }

                $fields = [$fieldKey => $field];
            }
        }

        return $fields;
    }

    public function getName() {
        if ($this->name !== null) {
            return $this->name;
        }

        return $this->attribute;
    }

    public function getColumns() {
        $column = $this->getColumn();
        if ($column === false) {
            return [];
        }

        $columnKey = $this->getName();
        if (!$columnKey) {
            $columnKey = 0;
        }

        $columns = [
            $columnKey => $column,
        ];

        return $columns;
    }

    public function getMultipleInputField() {
        if ($this->multipleInputField === false || !$this->attribute) {
            return false;
        }

        return ArrayHelper::merge([
            'name' => $this->attribute,
            'type' => $this->multipleInputType,
            'enableError' => true,
            'options' => [
                'placeholder' => $this->getLabel(),
            ],
            'title' => $this->getLabel(),
        ], $this->multipleInputField);
    }

    public function setField($field) {
        $this->_field = $field;

        return $this;
    }

    public function applyScopes(ActiveQuery $query) {
        $scopeResult = true;
        if ($this->scope !== false) {
            if ($this->scope !== null) {
                $scope = $this->scope;
                $scopeResult = $scope($query, $this->model);
            }

            if ($scopeResult && $this->attribute) {
                $this->_applyScopes($query);
            }
        }

        if ($scopeResult) {
            $this->applyRelationScopes($query);
        }

        return $query;
    }

    public function getIsRenderRelationFields() {
        if ($this->getDisplayOnly()) {
            return false;
        }

        if (is_callable($this->isRenderRelationFields)) {
            $isRenderRelationFields = $this->isRenderRelationFields;

            return $isRenderRelationFields($this);
        }

        return $this->isRenderRelationFields;
    }

    public function rules() {
        if ($this->rules === false) {
            return [];
        }

        $rules = [];
        if ($this->attribute !== null) {
            $rules = $this->getRules();
        }

        $rules = ArrayHelper::merge($rules, $this->rules);

        return $rules;
    }

    public function setLabel($label) {
        $this->_label = $label;

        return $this;
    }

    public function getLabel() {
        if ($this->_label !== null) {
            return $this->_label;
        }

        $attribute = $this->attribute;
        return $this->translateAttribute($attribute);
    }

    /**
     * @param ActiveQuery $query
     */
    protected function applyRelationScopes(ActiveQuery $query)
    {
        if ($this->relation) {
            return $this->getRelationObject()->applyScopes($query);
        }
    }

    public function getFormBuilderFields() {
        return [];
    }

    /**
     * @return array
     */
    protected function getRules(): array
    {
        $rules = [];
        $uniqueId = $this->attribute . $this->relation;
        if ($this->defaultValue !== null) {
            $rules[$uniqueId . 'DefaultValue'] = [
                [$this->attribute],
                'default',
                'value' => $this->defaultValue,
                'on' => $this->defaultScenario,
            ];
        }

        $rules[$uniqueId . 'SafeOnGrid'] = [
            [$this->attribute],
            'safe',
            'on' => self::SCENARIO_GRID,
        ];

        if (!$this->getReadOnly()) {
            if ($this->required) {
                $rule = 'required';
            } else {
                $rule = 'safe';
            }

            if ($this->getIsRenderRelationFields()) {
                $rules[$uniqueId . 'onFormAndDefault'] = [
                    [$this->relation],
                    $rule,
                    'on' => $this->defaultScenario,
                ];
            } else {
                $rules[$uniqueId . $rule . 'onFormAndDefault'] = [
                    [$this->attribute],
                    $rule,
                    'on' => $this->defaultScenario,
                ];
            }
        }
        return $rules;
    }

    protected function renderHasRelationFilter() {
        if (($relation = $this->getRelationObject()) && $this->isHasRelationAttribute) {
            return HasRelationDropdown::widget([
                'model' => $this->model,
                'attribute' => $this->isHasRelationAttribute,
                'parentId' => Html::getInputId($this->model, $this->attribute),
            ]);
        }
    }

    /**
     * @param $attribute
     * @return string
     */
    protected function translateAttribute($attribute): string
    {
        $attribute = Inflector::humanize($attribute, '_');
        if ($this->module === null) {
            return $attribute;
        }

        return \Yii::t('execut/' . $this->module, $attribute);
    }

    public function attributes() {
        return [];
    }

    /**
     * @param ActiveQuery $query
     */
    protected function _applyScopes(ActiveQuery $query)
    {
        $attribute = $this->attribute;
        $value = $this->getValue();
        if (is_array($value)) {
            $value = array_filter($value);
        }

        if (!empty($value) || $value === '0') {
            if (!($this->model instanceof \execut\oData\ActiveRecord)) {
                $whereAttribute = $this->model->tableName() . '.' . $attribute;
            } else {
                $whereAttribute = $attribute;
            }

            $query->andFilterWhere([
                $whereAttribute => $value,
            ]);
        }
    }
}