<?php
/**
 */

namespace execut\crudFields;


use execut\crudFields\fields\Field;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;
use yii\base\Behavior as BaseBehavior;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

class Behavior extends BaseBehavior
{
    protected $_plugins = [];
    public $module = null;

    /**
     * @param ActiveRecord $owner
     */
    public function attach($owner)
    {
        parent::attach($owner); // TODO: Change the autogenerated stub
        $relations = $this->getRelations();
        if (!empty($relations)) {
            $relationsSaver = $owner->getBehavior('relationsSaver');

            if (!$relationsSaver) {
                $owner->attachBehavior('relationsSaver', [
                    'class' => SaveRelationsBehavior::class,
                ]);
                $relationsSaver = $owner->getBehavior('relationsSaver');
//                throw new \execut\yii\base\Exception('RelationsSaver behavior is required. Define it between via config:
//                \'relationsSaver\' => [
//                    \'class\' => SaveRelationsBehavior::class,
//                    \'relations\' => [],
//                ],');
            }

            foreach ($relations as $relation => $relationParams) {
                if (!in_array($relation, $relationsSaver->relations)) {
                    $params = [];
                    if (!empty($relationParams['scenario'])) {
                        $params['scenario'] = $relationParams['scenario'];
                    }

                    $relationsSaver->addRelation($relation, $params);
                }
            }
        }
    }

    public function setPlugins($plugins) {
        $this->_plugins = $plugins;
    }

    protected $_pluginsIsInited = false;
    protected function initPlugins() {
        if (!$this->_pluginsIsInited) {
            foreach ($this->_plugins as $key => $plugin) {
                if (is_array($plugin)) {
                    $plugin = \yii::createObject($plugin);
                    $this->_plugins[$key] = $plugin;
                }

                if (!($plugin instanceof Plugin)) {
                    throw new Exception('Fields plugin ' . get_class($plugin) . ' must bee instance of ' . Plugin::class);
                }

                $plugin->owner = $this->owner;
            }

            $this->_pluginsIsInited = true;
        }
    }

    public function getPlugins() {
        $this->initPlugins();
        return $this->_plugins;
    }

    public function getRelations() {
        $relations = [];
        foreach ($this->getPlugins() as $plugin) {
            $relations = ArrayHelper::merge($relations, $plugin->getRelations());
        }

        return $relations;
    }

    public function getRelation($name) {
        $relations = $this->getRelations();
        if (!empty($relations[$name])) {
            $relation = $relations[$name];
            $relation['name'] = $name;

            return $relation;
        }
    }

    public function getPluginsFields() {
        $result = [];
        foreach ($this->plugins as $plugin) {
            $result = array_merge($result, $plugin->getFields());
        }

        return $result;
    }

    protected $_fields = [];
    public function setFields($fields) {
        $this->_fields = $fields;
        return $this;
    }

    public function getField($name) {
        $fields = $this->getFields();
        return $fields[$name];
    }

    protected $fieldsIsInitied = false;
    public function getFields() {
        if ($this->fieldsIsInitied) {
            return $this->_fields;
        }

        $fields = $this->_fields;
        $fields = array_merge($fields, $this->getPluginsFields());
        foreach ($fields as $key => $field) {
            if (is_string($field)) {
                if (@class_exists($field)) {
                    $field = ['class' => $field];
                } else {
                    $field = ['attribute' => $field];
                }
            }

            if (is_array($field)) {
                if (empty($field['class'])) {
                    $class = Field::class;
                } else {
                    $class = $field['class'];
                    unset($field['class']);
                }

                if (!is_subclass_of($class, Field::class) && $class !== Field::class) {
                    $this->throwFieldClassException($key, $class);
                }

                $field = new $class($field);
            } else {
                if (!($field instanceof Field)) {
                    $this->throwFieldClassException($key, $field);
                }
            }

            $field->model = $this->owner;
            $field->module = $this->module;
            $field->attach();

            $fields[$key] = $field;
        }

        uasort($fields, function ($a, $b) {
            return $a->order > $b->order;
        });

        $this->_fields = $fields;
        $this->fieldsIsInitied = true;

        return $fields;
    }

    public function getGridColumns() {
        $columns = [];
        foreach ($this->getFields() as $key => $field) {
            $fieldColumns = $field->getColumns();
            foreach ($fieldColumns as $column) {
                if ($column !== false) {
                    $columns[$key] = $column;
                }
            }
        }

        return $columns;
    }

    public function getFormFields() {
        $columns = [];
        foreach ($this->getFields() as $fieldKey => $field) {
            $formFields = $field->getFields();
            foreach ($formFields as $formFieldKey => $formField) {
                if ($formField !== false) {
                    $columns[$fieldKey . '_' . $formFieldKey] = $formField;
                }
            }
        }

        return $columns;
    }

    public function applyScopes($query) {
        foreach ($this->getFields() as $field) {
            $query = $field->applyScopes($query);
        }

        return $query;
    }

    public function search() {
        $modelClass = $this->owner->className();
        $q = $modelClass::find();
        $q = $this->applyScopes($q);
        $dataProvider = new ActiveDataProvider([
            'query' => $q,
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);
        $this->initDataProvider($dataProvider);

        return $dataProvider;
    }

    public function initDataProvider($dataProvider) {
        foreach ($this->getPlugins() as $plugin) {
            $plugin->initDataProvider($dataProvider);
        }
    }

    public function rules() {
        $rules = [];
        foreach ($this->getFields() as $field) {
            $fieldRules = $field->rules();
            if ($fieldRules !== false) {
                $rules = array_merge($rules, $fieldRules);
            }
        }

        foreach ($this->getPlugins() as $plugin) {
            $rules = array_merge($rules, $plugin->rules());
        }

        foreach ($this->getRelations() as $relation => $relationParams) {
            $rules[] = [$relation, 'safe'];
        }

        return $rules;
    }

    public function attributesLabels() {
        $result = [];
        foreach ($this->getFields() as $field) {
            $result[$field->attribute] = $field->getLabel($this->module);
        }

        return $result;
    }

    public function getMultipleInputFields() {
        $columns = [];
        foreach ($this->getFields() as $key => $field) {
            $field = $field->getMultipleInputField();
            if ($field !== false) {
                $columns[$key] = $field;
            }
        }

        return $columns;
    }

    protected function throwFieldClassException($key, $field): void
    {
        if (!is_string($field)) {
            $field = get_class($field);
        }

        throw new Exception('Field "' . $key . '" must bee instance of ' . Field::class . '. Instance of ' . $field . ' instead');
    }
}