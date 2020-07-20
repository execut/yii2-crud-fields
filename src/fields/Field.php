<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields;
use execut\crudFields\Relation;
use execut\crudFields\widgets\HasRelationDropdown;
use unclead\multipleinput\MultipleInputColumn;
use yii\base\BaseObject;
use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
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
    protected $required = false;
    public $defaultValue = null;
    protected $attribute = null;
    public $rules = [];
    public $multipleInputType = MultipleInputColumn::TYPE_TEXT_INPUT;
    protected $_column = [];
    protected $_field = [];
    protected $_label = null;
    protected $readOnly = null;
    protected $detailViewFieldClass = DetailViewField::class;
    public $isRenderRelationFields = false;
    public $isRenderInRelationForm = true;

    public $data = [];
    public $valueAttribute = null;
    public $multipleInputField = [];
    public $defaultScenario = self::SCENARIO_DEFAULT;
    public $name = null;

    /**
     * @var \Closure|null
     */
    public $scope = null;

    protected $_relationObject = null;
    protected $relationObjectParams = [
        'class' => Relation::class,
    ];

    public $order = 0;
    /**
     * @var ReloaderInterface[]
     */
    protected $reloaders = [];

    const SCENARIO_DEFAULT = [self::SCENARIO_FORM];

    public function __construct($config = [])
    {
        $relationAttributes = [
//            'attribute' => $this->attribute,
//            'model' => $this->model,
//            'isHasRelationAttribute' => $this->isHasRelationAttribute,
//            'label' => $this->getLabel(),

            'name' => 'relation',
            'query' => 'relationQuery',
            'nameAttribute',
            'orderByAttribute',
            'with',
//            'valueAttribute',
            'updateUrl',
            'url',
            'columnRecordsLimit',
            'isNoRenderRelationLink',
            'idAttribute',
            'urlMaker',
            'groupByVia',
        ];

        $relationValues = [];
        foreach ($config as $key => $value) {
            if (in_array($key, $relationAttributes)) {
                $relationValues[$key] = $value;
                unset($config[$key]);
            }
        }

        parent::__construct($config);
        if (!empty($relationValues) && !empty($relationValues['relation'])) {
            foreach ($relationValues as $key => $relationValue) {
                if (!is_string($relationAttribute = array_search($key, $relationAttributes))) {
                    $relationAttribute = $key;
                }

                $this->relationObjectParams[$relationAttribute] = $relationValue;
            }
        }
    }

    /**
     * @param ReloaderInterface $reloaders
     */
    public function setReloaders($reloaders)
    {
        $this->reloaders = $reloaders;
    }

    /**
     * @return ReloaderInterface[]
     */
    public function getReloaders()
    {
        return $this->reloaders;
    }

    public function addReloader($reloader) {
        $this->reloaders[] = $reloader;

        return $this;
    }

    protected function getRelationObjectParams() {
        return $this->relationObjectParams;
    }

    /**
     * @return mixed
     */
    protected function createRelationObject()
    {
        $relationObjectParams = $this->getRelationObjectParams();
        if (empty($relationObjectParams['name'])) {
            return false;
        }
        $params = ArrayHelper::merge([
            'field' => $this,
            'valueAttribute' => $this->valueAttribute,
            'attribute' => $this->attribute,
            'model' => $this->model,
            'isHasRelationAttribute' => $this->isHasRelationAttribute,
            'label' => $this->getLabel(),
        ], $relationObjectParams);
        $relation = \yii::createObject($params);

        return $relation;
    }

    public function getReadOnly() {
        if ($this->readOnly === null) {
            return $this->getDisplayOnly();
        }

        return $this->readOnly;
    }

    public function attach() {
    }

    public function setRelationObject($object) {
        $this->_relationObject = $object;

        return $this;
    }

    /**
     * @return Relation
     */
    public function getRelationObject() {
        if ($this->_relationObject === null) {
            $this->_relationObject = $this->createRelationObject();
        }

        return $this->_relationObject;
    }

    public function getRelation() {
        return $this->getRelationName();
    }

    public function getRelationQuery() {
        if ($relation = $this->getRelationObject()) {
            return $relation->getQuery();
        }
    }

    public function getRelationName() {
        if ($relation = $this->getRelationObject()) {
            return $relation->getName();
        }
    }

    public function getValue() {
        $this->initDefaultValue();
        $attribute = $this->attribute;
        if (empty($attribute)) {
            throw new Exception('"attribute" is required for getting value');
        }

        $model = $this->model;
        if (empty($model)) {
            throw new Exception('"model" is required for getting value');
        }

        return $model->$attribute;
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
            $fieldConfig = $this->getDetailViewFieldConfig();

            $this->detailViewField = new $this->detailViewFieldClass($fieldConfig, $this->attribute);
            $this->initDetailViewField($this->detailViewField);
        }

        return $this->detailViewField;
    }

    protected function getDetailViewFieldConfig() {
        return $this->_field;
    }

    protected function initDetailViewField(DetailViewField $field) {
    }

    public function setFieldConfig($config) {

        if (is_callable($config)) {
            $config = function ($model, $detailViewField) use ($config) {
                return $config($model, $this, $detailViewField);
            };
        }

        $this->getDetailViewField()->setFieldConfig($config);

        return $this;
    }

    public function getFieldConfig() {
        return $this->getDetailViewField()->getFieldConfig();
    }

    public function setDisplayOnly($displayOnly) {
        $this->getDetailViewField()->setDisplayOnly($displayOnly);
        return $this;
    }

    public function setReadOnly($readOnly) {
        $this->readOnly = $readOnly;
        return $this;
    }

    public function getDisplayOnly() {
        return $this->getDetailViewField()->getDisplayOnly();
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
        return $this->setFieldConfig($field);
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
        if ($relation = $this->getRelationObject()) {
            return $relation->applyScopes($query);
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
        $uniqueId = $this->attribute . $this->getRelationName();
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

        if ($this->isHasRelationAttribute) {
            $rules[$uniqueId . 'HasRelationSafeOnGrid'] = [
                [$this->isHasRelationAttribute],
                'safe',
                'on' => self::SCENARIO_GRID,
            ];
        }

        if (!$this->getReadOnly()) {
            if ($this->required) {
                $rule = 'required';
            } else {
                $rule = 'safe';
            }

            if ($this->getIsRenderRelationFields()) {
                $rules[$uniqueId . 'onFormAndDefault'] = [
                    [$this->getRelationName()],
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

    /**
     * @return string
     */
    protected function getRelationClass(): string
    {
        return Relation::class;
    }

    public function getUrl() {
        $relation = $this->getRelationObject();
        if ($relation) {
            return $relation->getUrl();
        }
    }

    public function getUrlMaker() {
        $relation = $this->getRelationObject();
        if ($relation) {
            return $relation->getUrlMaker();
        }
    }

    /**
     * @param null $attribute
     */
    public function setAttribute($attribute): void
    {
        $this->attribute = $attribute;
        if ($detailViewField = $this->detailViewField) {
            $detailViewField->setAttribute($attribute);
        }
    }

    public function getAttribute() {
        return $this->attribute;
    }

    /**
     * @param bool $required
     */
    public function setRequired(bool $required): void
    {
        $this->required = $required;
    }

    public function getRequired() {
        return $this->required;
    }

    /**
     * @return string
     */
    public function getDetailViewFieldClass(): string
    {
        return $this->detailViewFieldClass;
    }

    /**
     * @param string $detailViewFieldClass
     */
    public function setDetailViewFieldClass(string $detailViewFieldClass): self
    {
        $this->detailViewField = null;
        $this->detailViewFieldClass = $detailViewFieldClass;

        return $this;
    }

    protected function initDefaultValue(): void
    {
        if ($this->defaultValue !== null && in_array($this->model->getScenario(), $this->defaultScenario)) {
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
}