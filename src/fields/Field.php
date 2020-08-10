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
use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\MultipleInputColumn;
use yii\base\BaseObject;
use yii\base\Exception;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Inflector;

/**
 * Base class for CRUD fields. Simple text CRUD field with unlimited text length
 *
 * @package execut\crudFields
 */
class Field extends BaseObject
{
    /**
     * Scenario for filtering GridView
     */
    const SCENARIO_GRID = 'grid';
    /**
     * Scenario for DetailView
     */
    const SCENARIO_FORM = 'form';

    /**
     * Default scenarios list
     */
    const SCENARIO_DEFAULT = [self::SCENARIO_FORM];
    /**
     * @var string CRUD module id for field messages translations
     */
    public ?string $module = null;
    /**
     * @var bool|string The name of the IsHasRelation attribute to filter by the presence of records in the relation
     */
    public $isHasRelationAttribute = false;
    /**
     * @var ActiveRecord Current form or filter model
     */
    public $model = null;
    /**
     * @var mixed Default value of field. Can be a closure
     */
    public $defaultValue = null;
    /**
     * @var array Advanced field validation rules. False to completely disable rules generation
     */
    public $rules = [];
    /**
     * @var string Type of field multiple input for rendering MultipleInput widget
     * @see MultipleInput
     */
    public $multipleInputType = MultipleInputColumn::TYPE_TEXT_INPUT;
    /**
     * @var bool Show relationship form?
     */
    public $isRenderRelationFields = false;
    /**
     * @var bool The field visibility inside relationship form
     */
    public $isRenderInRelationForm = true;
    /**
     * @var int Field order
     */
    public $order = 0;
    /**
     * @var array|callable List of items to select within the field. Can be a closure
     */
    public $data = [];
    /**
     * @var string|null Attribute name for field value
     */
    public $valueAttribute = null;
    /**
     * @var array Multiple input config for rendering MultipleInput widget
     * @see MultipleInput
     */
    public $multipleInputField = [];
    /**
     * @var string[]
     */
    public $defaultScenario = self::SCENARIO_DEFAULT;
    /**
     * @var string|null CRUD field name
     */
    public $name = null;

    /**
     * @var \Closure|null Scope for query filtration. False for disable scopes
     */
    public $scope = null;
    /**
     * @var bool Field is required for form scenario
     */
    protected $required = false;
    /**
     * @var string Attribute name of CRUD field
     */
    protected $attribute = null;
    /**
     * @var array Redefined column config
     */
    protected $_column = [];
    /**
     * @var array Redefined field config
     */
    protected $_field = [];
    /**
     * @var string Field label
     */
    protected $_label = null;
    /**
     * @var boolean|callable|null Field is read only inside GridView
     */
    protected $readOnly = null;
    /**
     * @var string DetailViewField class
     */
    protected $detailViewFieldClass = DetailViewField::class;

    /**
     * @var Relation|null Relation object if it exists
     */
    protected $_relationObject = null;
    /**
     * @var array Relation object params by default
     */
    protected $relationObjectParams = [
        'class' => Relation::class,
    ];
    /**
     * @var DetailViewField Current detailViewField
     */
    protected $detailViewField = null;
    /**
     * @var ReloaderInterface[]
     */
    protected $reloaders = [];

    /**
     * Field constructor.
     * {@inheritdoc}
     */
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
     * Set field reloaders
     * @param ReloaderInterface $reloaders
     * @link https://github.com/execut/yii2-crud-fields/blob/master/docs/guide/reloaders.md
     */
    public function setReloaders($reloaders)
    {
        $this->reloaders = $reloaders;
    }

    /**
     * Returns CRUD field reloaders
     * @return ReloaderInterface[]
     */
    public function getReloaders()
    {
        return $this->reloaders;
    }

    /**
     * Add new field reloader
     * @param ReloaderInterface[] $reloader
     * @return $this
     */
    public function addReloader($reloader)
    {
        $this->reloaders[] = $reloader;

        return $this;
    }

    /**
     * Returns field is read only flag
     *
     * @return bool|callable|null
     */
    public function getReadOnly()
    {
        if ($this->readOnly === null) {
            return $this->getDisplayOnly();
        }

        return $this->readOnly;
    }

    /**
     * Called when a field has been attached to the model.
     */
    public function attach()
    {
    }

    /**
     * Set relation object
     * @param Relation $object Relation object
     * @return $this
     */
    public function setRelationObject($object)
    {
        $this->_relationObject = $object;

        return $this;
    }

    /**
     * Returns relation object
     * @return Relation
     */
    public function getRelationObject()
    {
        if ($this->_relationObject === null) {
            $this->_relationObject = $this->createRelationObject();
        }

        return $this->_relationObject;
    }

    /**
     * Get relation name
     * @return string
     */
    public function getRelation()
    {
        return $this->getRelationName();
    }

    /**
     * Get query of relation object
     * @return \yii\db\ActiveQueryInterface
     * @throws \yii\db\Exception
     */
    public function getRelationQuery()
    {
        if ($relation = $this->getRelationObject()) {
            return $relation->getQuery();
        }

        return null;
    }

    /**
     * Get name of relation object
     * @return string|null
     */
    public function getRelationName()
    {
        if ($relation = $this->getRelationObject()) {
            return $relation->getName();
        }

        return null;
    }

    /**
     * Returns calculated value of field
     * @return mixed|null
     * @throws Exception
     */
    public function getValue()
    {
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

    /**
     * Returns field data, items to select
     * @return array|callable
     */
    public function getData()
    {
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

    /**
     * Returns the configuration of a gridView column
     * @return array|bool
     */
    public function getColumn()
    {
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

    /**
     * Set the configuration of a gridView column
     * @param array $column
     * @return $this
     */
    public function setColumn($column)
    {
        $this->_column = $column;

        return $this;
    }

    /**
     * Returns the field configuration for DetailView
     * @return array|bool
     */
    public function getField()
    {
        return $this->getDetailViewField()->getConfig($this->model);
    }


    /**
     * Set the DetailViewField object
     * @param DetailViewField $detailViewField
     * @return $this
     */
    public function setDetailViewField($detailViewField)
    {
        $this->detailViewField = $detailViewField;
        return $this;
    }

    /**
     * Returns the DetailViewField object for CRUD field
     * @return DetailViewField|null
     */
    public function getDetailViewField()
    {
        if ($this->detailViewField === null) {
            $fieldConfig = $this->getDetailViewFieldConfig();

            $this->detailViewField = new $this->detailViewFieldClass($fieldConfig, $this->attribute);
            $this->initDetailViewField($this->detailViewField);
        }

        return $this->detailViewField;
    }

    /**
     * Set the configuration of the DetailViewField object
     * @param array|callable $config
     * @return $this
     */
    public function setFieldConfig($config)
    {
        if (is_callable($config)) {
            $config = function ($model, $detailViewField) use ($config) {
                return $config($model, $this, $detailViewField);
            };
        }

        $this->getDetailViewField()->setFieldConfig($config);

        return $this;
    }

    /**
     * Get the configuration of the DetailViewField object
     * @return array|null
     */
    public function getFieldConfig()
    {
        return $this->getDetailViewField()->getFieldConfig();
    }

    /**
     * Set the displayOnly flag of DetailViewField object
     * @param boolean|callable $displayOnly
     * @return $this
     */
    public function setDisplayOnly($displayOnly)
    {
        $this->getDetailViewField()->setDisplayOnly($displayOnly);
        return $this;
    }

    /**
     * Set the readOnly field flag
     * @param $readOnly
     * @return $this
     */
    public function setReadOnly($readOnly)
    {
        $this->readOnly = $readOnly;
        return $this;
    }

    /**
     * Get the displayOnly flag of DetailViewField object
     * @return bool
     */
    public function getDisplayOnly()
    {
        return $this->getDetailViewField()->getDisplayOnly();
    }

    /**
     * Returns field name
     * @return string|null
     */
    public function getName()
    {
        if ($this->name !== null) {
            return $this->name;
        }

        return $this->attribute;
    }

    /**
     * Returns attributes configuration for the CRUD DetailView widget
     * @param bool $isWithRelationsFields Is return also relations fields
     * @return array
     */
    public function getFields($isWithRelationsFields = true)
    {
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

    /**
     * Returns configuration for the GridView CRUD
     * @return array
     */
    public function getColumns()
    {
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

    /**
     * Returns the field configuration for the MultipleInput widget
     * @return array|bool
     */
    public function getMultipleInputField()
    {
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

    /**
     * Set the configuration of the DetailViewField object
     * @param array|callable $field Field configuration
     * @return $this
     */
    public function setField($field)
    {
        return $this->setFieldConfig($field);
    }

    /**
     * Apply query scopes for DataProvider
     * @param ActiveQueryInterface $query
     * @return ActiveQueryInterface
     * @throws Exception
     */
    public function applyScopes(ActiveQueryInterface $query)
    {
        $scopeResult = true;
        if ($this->scope !== false) {
            if ($this->scope !== null) {
                $scope = $this->scope;
                $scopeResult = $scope($query, $this->model);
            }

            if ($scopeResult && $this->attribute) {
                $this->applyFieldScopes($query);
            }
        }

        if ($scopeResult) {
            $this->applyRelationScopes($query);
        }

        return $query;
    }

    /**
     * Calculates and returns whether the output of the relationship subform is required
     * @return bool
     */
    public function getIsRenderRelationFields()
    {
        if ($this->getDisplayOnly()) {
            return false;
        }

        if (is_callable($this->isRenderRelationFields)) {
            $isRenderRelationFields = $this->isRenderRelationFields;

            return $isRenderRelationFields($this);
        }

        return $this->isRenderRelationFields;
    }

    /**
     * Returns model validation rules
     * @return array
     */
    public function rules()
    {
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

    /**
     * Set CRUD field label
     * @param string $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->_label = $label;

        return $this;
    }

    /**
     * Returns translated CRUD field label
     * @return string|null
     */
    public function getLabel()
    {
        if ($this->_label !== null) {
            return $this->_label;
        }

        $attribute = $this->attribute;
        return $this->translateAttribute($attribute);
    }

    /**
     * Returns the URL from a relationship object
     * @return string|array|null
     */
    public function getUrl()
    {
        $relation = $this->getRelationObject();
        if ($relation) {
            return $relation->getUrl();
        }

        return null;
    }

    /**
     * Returns the URL maker from a relationship object
     *
     * @return \execut\crudFields\relation\UrlMaker|null
     */
    public function getUrlMaker()
    {
        $relation = $this->getRelationObject();
        if ($relation) {
            return $relation->getUrlMaker();
        }

        return null;
    }

    /**
     * Set the attribute name
     * @param null $attribute The attribute name
     */
    public function setAttribute($attribute): void
    {
        $this->attribute = $attribute;
        if ($detailViewField = $this->detailViewField) {
            $detailViewField->setAttribute($attribute);
        }
    }

    /**
     * Get the attribute name
     * @return string|null
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * Set field as required or not
     * @param bool $required Required flag
     */
    public function setRequired(bool $required): void
    {
        $this->required = $required;
    }

    /**
     * Get field is required or not
     * @return bool
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * Returns the class of DetailViewField object
     * @return string
     */
    public function getDetailViewFieldClass(): string
    {
        return $this->detailViewFieldClass;
    }

    /**
     * Set the class of DetailViewField object
     * @param string $detailViewFieldClass The class of DetailViewField object
     * @return $this
     */
    public function setDetailViewFieldClass(string $detailViewFieldClass): self
    {
        $this->detailViewField = null;
        $this->detailViewFieldClass = $detailViewFieldClass;

        return $this;
    }

    /**
     * Returns the default configuration of DetailViewField object
     * @return array
     */
    protected function getRelationObjectParams()
    {
        return $this->relationObjectParams;
    }

    /**
     * Factory of DetailViewField object
     * @return DetailViewField|false
     * @throws \yii\base\InvalidConfigException
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
//            'label' => $this->getLabel(),
        ], $relationObjectParams);
        /**
         * @var DetailViewField $relation
         */
        $relation = \yii::createObject($params);

        return $relation;
    }


    /**
     * Returns the current configuration of DetailViewField object
     * @return array
     */
    protected function getDetailViewFieldConfig()
    {
        return $this->_field;
    }

    /**
     * Initialization of DetailViewField object
     * @param DetailViewField $field Field object
     */
    protected function initDetailViewField(DetailViewField $field)
    {
    }

    /**
     * Apply relation object scopes
     * @param ActiveQueryInterface $query
     * @return ActiveQueryInterface|null
     */
    protected function applyRelationScopes(ActiveQueryInterface $query)
    {
        if ($relation = $this->getRelationObject()) {
            return $relation->applyScopes($query);
        }

        return null;
    }

    /**
     * Return rules
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

    /**
     * Returns the rendered has relation filter
     * @return string
     * @throws \Exception
     */
    protected function renderHasRelationFilter()
    {
        if (($relation = $this->getRelationObject()) && $this->isHasRelationAttribute) {
            return HasRelationDropdown::widget([
                'model' => $this->model,
                'attribute' => $this->isHasRelationAttribute,
                'parentId' => Html::getInputId($this->model, $this->attribute),
            ]);
        }

        return null;
    }

    /**
     * Translate message via i18n
     * @param string $attribute Message for translation
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

    /**
     * Accept field specific scopes
     * @param ActiveQueryInterface $query
     * @throws Exception
     */
    protected function applyFieldScopes(ActiveQueryInterface $query)
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
     * Set a default value for a model attribute if empty
     */
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
