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

class Behavior extends BaseBehavior
{
    protected $_plugins = [];
    protected $_module = null;
    public $relations = [];
    public $rules = [];
    public $roles = [];
    protected $role = null;
    const RELATIONS_SAVER_KEY = 'relationsSaver';
    public $defaultScenario = Field::SCENARIO_DEFAULT;
    public $relationsSaver = [];

    public function setRole($role) {
        $this->role = $role;
        $this->fieldsIsInitied = false;
    }

    /**
     * @param ActiveRecord $owner
     */
    public function attach($owner)
    {
        parent::attach($owner);
        $relations = $this->getRelations();
        if (!empty($this->relationsSaver)) {
            /**
             * @var SaveRelationsBehavior $relationsSaver
             */
            $relationsSaver = $this->relationsSaver;
            if (!isset($relationsSaver['class'])) {
                $relationsSaver['class'] = SaveRelationsBehavior::class;
            }

            $relationsSaver = \yii::createObject($relationsSaver);
            $owner->attachBehavior(self::RELATIONS_SAVER_KEY, $relationsSaver);
        }

        if (!empty($relations)) {
            $relationsSaver = $owner->getBehavior(self::RELATIONS_SAVER_KEY);
            if (!$relationsSaver) {
                $owner->attachBehavior(self::RELATIONS_SAVER_KEY, [
                    'class' => SaveRelationsBehavior::class,
                ]);
                $relationsSaver = $owner->getBehavior(self::RELATIONS_SAVER_KEY);
//                throw new \execut\yii\base\Exception('RelationsSaver behavior is required. Define it between via config:
//                \'relationsSaver\' => [
//                    \'class\' => SaveRelationsBehavior::class,
//                    \'relations\' => [],
//                ],');
            }

            foreach ($relations as $relation => $relationParams) {
                if (!in_array($relation, $relationsSaver->relations) && empty($relationParams['isNoSave'])) {
                    $params = [];
                    if (!empty($relationParams['scenario'])) {
                        $params['scenario'] = $relationParams['scenario'];
                    }

                    $relationsSaver->addRelation($relation, $params);
                }
            }
        }

        foreach ($this->getPlugins() as $plugin) {
            $plugin->attach();
        }
    }

    public function events()
    {
        $events = [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
            ActiveRecord::EVENT_BEFORE_DELETE => 'beforeDelete',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeInsert',
        ];

        return $events;
    }

    public function beforeValidate() {
        $this->setRelationsScenarioFromOwner();
        foreach ($this->getPlugins() as $plugin) {
            $plugin->beforeValidate();
        }
    }

    public function beforeUpdate() {
        foreach ($this->getPlugins() as $plugin) {
            $plugin->beforeUpdate();
        }

        $this->beforeSave();
    }

    public function beforeInsert() {
        foreach ($this->getPlugins() as $plugin) {
            $plugin->beforeInsert();
        }

        $this->beforeSave();
    }

    public function beforeSave() {
        foreach ($this->getPlugins() as $plugin) {
            $plugin->beforeSave();
        }
    }

    public function afterInsert() {
        foreach ($this->getPlugins() as $plugin) {
            $plugin->afterInsert();
        }

        $this->afterSave();
    }

    public function afterUpdate() {
        foreach ($this->getPlugins() as $plugin) {
            $plugin->afterUpdate();
        }
        $this->afterSave();
    }

    public function afterSave() {
        foreach ($this->getPlugins() as $plugin) {
            $plugin->afterSave();
        }
    }

    public function beforeDelete() {
        foreach ($this->getPlugins() as $plugin) {
            $plugin->beforeDelete();
        }
    }

    public function setRelationsScenarioFromOwner() {
        /**
         * @var SaveRelationsBehavior $saver
         */
        $saver = $this->owner->getBehavior(self::RELATIONS_SAVER_KEY);
        if ($saver) {
            foreach ($saver->relations as $relation) {
                $scenario = $this->owner->getScenario();
                $saver->setRelationScenario($relation, $scenario);
            }

            foreach ($this->getRelations() as $name => $relation) {
                if (empty($relation['scenario'])) {
                    if (!empty($relation['name'])) {
                        $name = $relation['name'];
                    }

                    $scenario = $this->owner->getScenario();
                    $saver->setRelationScenario($name, $scenario);
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
                if (is_string($plugin)) {
                    $plugin = ['class' => $plugin];
                }

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

    /**
     * @return Plugin[]
     * @throws Exception
     */
    public function getPlugins() {
        $this->initPlugins();
        return $this->_plugins;
    }

    public function getPlugin($key) {
        $plugins = $this->getPlugins();
        if (array_key_exists($key, $plugins)) {
            return $plugins[$key];
        }
    }

    public function getRelations() {
        $relations = $this->relations;
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
        foreach ($this->getPlugins() as $plugin) {
            $result = array_merge($result, $plugin->getFields());
        }

        return $result;
    }

    protected $_fields = [];
    protected $_fieldsConfig = [];
    public function setFields($fields) {
        $this->_fieldsConfig = $fields;
        return $this;
    }

    public function getField($name) {
        $fields = $this->getFields();
        if (array_key_exists($name, $fields)) {
            return $fields[$name];
        }
    }

    public function getRowOptions() {
        $plugins = $this->getPlugins();
        $options = [];
        foreach ($plugins as $plugin) {
            if ($plugin instanceof RowOptionsPlugin) {
                $options = ArrayHelper::merge($options, $plugin->getRowOptions());
            }
        }

        return $options;
    }

    protected $fieldsIsInitied = false;

    /**
     * @return Field[]
     * @throws Exception
     */
    public function getFields() {
        if ($this->fieldsIsInitied) {
            return $this->_fields;
        }

        $fields = $this->getFieldsConfig();
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

            $field->defaultScenario = $this->defaultScenario;
            $field->model = $this->owner;
            if ($field->module === null) {
                $field->module = $this->module;
            }

            $field->attach();

            $fields[$key] = $field;
        }

//        $orderedFields = array_filter($fields, function ($field) {
//            return $field->order != 0;
//        });
//
//        $unOrderedFields = array_filter($fields, function ($field) {
//            return $field->order == 0;
//        });

        $orderedFields = [];
        foreach ($fields as $key => $field) {
            if (!isset($orderedFields[$field->order])) {
                $orderedFields[$field->order] = [];
            }

            $orderedFields[$field->order][$key] = $field;
        }

        ksort($orderedFields);
        $fields = [];
        foreach ($orderedFields as $orderedField) {
            $fields = array_merge($fields, $orderedField);
        }

//        $fields = array_merge($orderedFields, array_filter($fields, function ($field) {
//            return $field->order == 0;
//        }));

        $this->_fields = $fields;
        $this->fieldsIsInitied = true;

        return $fields;
    }

    public function getGridColumns() {
        $columns = [];
        $fields = $this->getFields();
        foreach ($fields as $fieldKey => $field) {
            $fieldColumns = $field->getColumns();
            foreach ($fieldColumns as $key => $column) {
                if ($column !== false) {
                    if ($key > 0 || $key === 0) {
                        $columns[] = $column;
                    } else {
                        if (array_key_exists($key, $columns)) {
//                            throw new Exception('Column key "' . $key . '" is already existed. Set other column key for field "' . $fieldKey . '"');
                        }

                        $columns[$key] = $column;
                    }
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

    public function getScopes() {
        $scopes = $this->scopes;
//        $scopes = array_merge($scopes, $this->getPluginsScopes());
        if ($this->role !== null && !empty($this->roles[$this->role]) && !empty($this->roles[$this->role]['scopes'])) {
            $scopes = ArrayHelper::merge($scopes, $this->roles[$this->role]['scopes']);
        }

        return $scopes;
    }

    public $scopes = [];
    protected $query = null;
    public function setQuery($q) {
        $this->query = $q;

        return $this;
    }

    public function getQuery() {
        if ($this->query === null) {
            $modelClass = get_class($this->owner);
            $this->query = $modelClass::find();
        }

        return $this->query;
    }
    public function applyScopes($query) {
        if (!empty($this->scopes)) {
            $scopes = $this->scopes;
            foreach ($scopes as $scope) {
                $scope($query);
            }
        }
        foreach ($this->getFields() as $field) {
            $query = $field->applyScopes($query);
        }

        foreach ($this->getPlugins() as $plugin) {
            $pluginResult = $plugin->applyScopes($query);
            if ($pluginResult !== null) {
                $query = $pluginResult;
            }
        }

        return $query;
    }

    public function search() {
        $q = $this->getQuery();
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
                $rules = ArrayHelper::merge($rules, $fieldRules);
            }
        }

        foreach ($this->getPlugins() as $plugin) {
            $rules = ArrayHelper::merge($rules, $plugin->rules());
        }

        foreach ($this->getRelations() as $relation => $relationParams) {
            $rules[$relation . 'Safe'] = [$relation, 'safe'];
        }

        $rules = ArrayHelper::merge($this->rules, $rules);
        $forOrderRules = [];
        foreach ($rules as $key => $rule) {
            if (array_key_exists('order', $rule)) {
                $forOrderRules[$key] = $rule;
                unset($rules[$key]);
            }
        }

        uasort($forOrderRules, function ($a, $b) {
            if ($a['order'] > $b['order']) {
                return -1;
            } else {
                return 1;
            }

            return;
        });

        foreach ($forOrderRules as &$rule) {
            unset($rule['order']);
        }

        return ArrayHelper::merge($rules, $forOrderRules);
    }

    public function setModule($module) {
        $this->_module = $module;

        return $this;
    }

    public function getModule() {
        if ($this->_module === null) {
            return $this->detectModule();
        }

        return $this->_module;
    }

    public function detectModule() {
        if (!$this->owner) {
            return;
        }

        $ownerClass = get_class($this->owner);
        $parts = explode('\\', $ownerClass);
        if (!empty($parts[1])) {
            return $parts[1];
        }
    }

    public function attributesLabels() {
        $result = [];
        foreach ($this->getFields() as $field) {
            $result[$field->getName()] = $field->getLabel($this->module);
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

    /**
     * @return array
     */
    protected function getFieldsConfig(): array
    {
        $fields = $this->_fieldsConfig;
        $fields = array_merge($fields, $this->getPluginsFields());
        if ($this->role !== null && !empty($this->roles[$this->role]) && !empty($this->roles[$this->role]['fields'])) {
            $fields = ArrayHelper::merge($fields, $this->roles[$this->role]['fields']);
        }

        return $fields;
    }
}