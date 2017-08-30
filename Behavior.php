<?php
/**
 */

namespace execut\crudFields;


use execut\crudFields\fields\Field;
use yii\base\Behavior as BaseBehavior;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\helpers\Inflector;

class Behavior extends BaseBehavior
{
    protected $_plugins = [];
    public $module = null;
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

    public function getRelation($name) {
        foreach ($this->getPlugins() as $plugin) {
            $relations = $plugin->getRelations();
            if (!empty($relations[$name])) {
                return $relations[$name];
            }
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

    public function getFields() {
        $fields = $this->_fields;

        $fields = array_merge($fields, $this->getPluginsFields());
        foreach ($fields as $key => $field) {
            if (is_string($field)) {
                if (class_exists($field, false)) {
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

                $field = new $class($field);
            }

            $field->model = $this->owner;
            $field->module = $this->module;

            $fields[$key] = $field;
        }

        uasort($fields, function ($a, $b) {
            return $a->order > $b->order;
        });

        return $fields;
    }

    public function getGridColumns() {
        $columns = [];
        foreach ($this->getFields() as $key => $field) {
            $column = $field->getColumn();
            if ($column !== false) {
                $columns[$key] = $column;
            }
        }

        return $columns;
    }

    public function getFormFields() {
        $columns = [];
        foreach ($this->getFields() as $key => $field) {
            $field = $field->getField();
            if ($field !== false) {
                $columns[$key] = $field;
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
}