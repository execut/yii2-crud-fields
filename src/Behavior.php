<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields;

use execut\actions\widgets\DynaGrid;
use execut\crudFields\fields\Field;
use kartik\detail\DetailView;
use kartik\grid\GridView;
use unclead\multipleinput\MultipleInput;
use \yii\base\Model;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;
use yii\base\Behavior as BaseBehavior;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Class Behavior
 * @package execut\crudFields
 */
class Behavior extends BaseBehavior
{
    /**
     * @var string Relation saver behavior key within model
     */
    const RELATIONS_SAVER_KEY = 'relationsSaver';
    /**
     * @var string Fields behavior key within model
     */
    const KEY = 'fields';
    const EVENT_BEFORE_LOAD = 'beforeLoad';
    const EVENT_AFTER_LOAD = 'afterLoad';
    /**
     * @var Model The owner of this behavior
     */
    public $owner = null;
    /**
     * @var array Advanced validation rules
     */
    public $rules = [];
    /**
     * @var string[] Default model scenarios list
     */
    public $defaultScenario = Field::SCENARIO_DEFAULT;
    /**
     * @var array Relations saver behavior configuration
     */
    public $relationsSaver = [];
    /**
     * @var array Advanced scopes
     */
    public $scopes = [];
    /**
     * @var bool Plugins is initialized
     */
    protected $_pluginsIsInited = false;
    /**
     * @var Field[] Initialized fields instances
     */
    protected $_fields = [];
    /**
     * @var bool Fields configuration
     */
    protected $_fieldsConfig = [];
    /**
     * @var bool Fields is initialized from config
     */
    protected $fieldsIsInitied = false;
    /**
     * @var ActiveQueryInterface Query for DataProvider
     */
    protected $query = null;
    /**
     * @var array Plugins configuration or instances
     */
    protected $_plugins = [];
    /**
     * @var string Module id for translations
     */
    protected $_module = null;

    /**
     * {@inheritDoc}
     */
    public function attach($owner)
    {
        parent::attach($owner);
        foreach ($this->getPlugins() as $plugin) {
            $plugin->attach();
        }
    }

    /**
     * Set relations scenario from owner
     */
    public function setRelationsScenarioFromOwner()
    {
        $this->initSaverBehaviorRelations();
        /**
         * @var SaveRelationsBehavior $saver
         */
        $saver = $this->owner->getBehavior(self::RELATIONS_SAVER_KEY);
        if ($saver) {
            foreach ($saver->relations as $relation) {
                $scenario = $this->owner->getScenario();
                $saver->setRelationScenario($relation, $scenario);
            }

            foreach ($this->getRelationsNames() as $name) {
                $scenario = $this->owner->getScenario();
                $saver->setRelationScenario($name, $scenario);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function events()
    {
        $events = [
            ActiveRecord::EVENT_INIT => 'initEvent',
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
            ActiveRecord::EVENT_AFTER_VALIDATE => 'afterValidate',
            ActiveRecord::EVENT_BEFORE_DELETE => 'beforeDelete',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeInsert',
            self::EVENT_AFTER_LOAD => 'afterLoad',
            self::EVENT_BEFORE_LOAD => 'beforeLoad',
        ];

        return $events;
    }

    /**
     * Init event
     */
    public function initEvent()
    {
        $owner = $this->owner;
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
    }

    /**
     * Before validate
     * @throws Exception
     */
    public function beforeValidate()
    {
        $this->setRelationsScenarioFromOwner();
        foreach ($this->getPlugins() as $plugin) {
            $plugin->beforeValidate();
        }
    }

    /**
     * Before update
     * @throws Exception
     */
    public function beforeUpdate()
    {
        foreach ($this->getPlugins() as $plugin) {
            $plugin->beforeUpdate();
        }

        $this->beforeSave();
    }

    /**
     * After validate
     * @throws Exception
     */
    public function afterValidate()
    {
        foreach ($this->getPlugins() as $plugin) {
            $plugin->afterValidate();
        }
    }

    /**
     * Before insert
     * @throws Exception
     */
    public function beforeInsert()
    {
        foreach ($this->getPlugins() as $plugin) {
            $plugin->beforeInsert();
        }

        $this->beforeSave();
    }

    /**
     * Before save
     * @throws Exception
     */
    public function beforeSave()
    {
        foreach ($this->getPlugins() as $plugin) {
            $plugin->beforeSave();
        }
    }

    /**
     * After insert
     * @throws Exception
     */
    public function afterInsert()
    {
        foreach ($this->getPlugins() as $plugin) {
            $plugin->afterInsert();
        }

        $this->afterSave();
    }

    /**
     * After update
     * @throws Exception
     */
    public function afterUpdate()
    {
        foreach ($this->getPlugins() as $plugin) {
            $plugin->afterUpdate();
        }
        $this->afterSave();
    }

    /**
     * After save
     * @throws Exception
     */
    public function afterSave()
    {
        foreach ($this->getPlugins() as $plugin) {
            $plugin->afterSave();
        }
    }

    /**
     * Before delete
     * @throws Exception
     */
    public function beforeDelete()
    {
        foreach ($this->getPlugins() as $plugin) {
            $plugin->beforeDelete();
        }
    }

    /**
     * Before load
     */
    public function beforeLoad()
    {
        $this->setRelationsScenarioFromOwner();
    }

    /**
     * After load
     * @throws Exception
     */
    public function afterLoad()
    {
        foreach ($this->getPlugins() as $plugin) {
            $plugin->afterLoad();
        }
    }


    /**
     * Set plugins instances or configurations arrays
     * @param $plugins
     */
    public function setPlugins($plugins)
    {
        $this->_plugins = $plugins;
    }

    /**
     * {@inheritDoc}
     */
    public function canGetProperty($name, $checkVars = true)
    {
        if (parent::canGetProperty($name, $checkVars)) {
            return true;
        }

        return $this->isHasRelation($name);
    }

    /**
     * {@inheritDoc}
     */
    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }

        if ($this->isHasRelation($name)) {
            return $this->getRelation($name);
        }

        return parent::__get($name);
    }

    /**
     * Returns fields plugins
     * @return Plugin[]
     * @throws Exception
     */
    public function getPlugins()
    {
        $this->initPlugins();
        return $this->_plugins;
    }

    /**
     * Returns plugin instance by key
     * @param string $key
     * @return Plugin
     * @throws Exception
     */
    public function getPlugin($key)
    {
        $plugins = $this->getPlugins();
        if (array_key_exists($key, $plugins)) {
            return $plugins[$key];
        }
    }

    /**
     * Returns model relation by key
     * @param $name Relation name
     * @return ActiveQueryInterface|null
     * @throws Exception
     * @throws \yii\db\Exception
     */
    public function getRelation($name)
    {
        if ($field = $this->getField($name)) {
            $q = $field->getRelationQuery();
            if ($q) {
                return $q;
            }
        }

        return $this->getPluginsRelation($name);
    }


    /**
     * Set fields configs or instances
     * @param array $fields Fields configs or instances
     * @return $this
     */
    public function setFields($fields)
    {
        $this->_fieldsConfig = $fields;
        return $this;
    }

    /**
     * Returns field instance by name
     * @param string $name Field name
     * @return Field
     * @throws Exception
     */
    public function getField($name)
    {
        $fields = $this->getFields();
        if (array_key_exists($name, $fields)) {
            return $fields[$name];
        }

        return null;
    }

    /**
     * Returns rowOptions for DynaGrid
     * @see DynaGrid
     * @return array
     * @throws Exception
     */
    public function getRowOptions()
    {
        $plugins = $this->getPlugins();
        $options = [];
        foreach ($plugins as $plugin) {
            if ($plugin instanceof RowOptionsPlugin) {
                $options = ArrayHelper::merge($options, $plugin->getRowOptions());
            }
        }

        return $options;
    }


    /**
     * Returns fields instances
     * @return Field[]
     * @throws Exception
     */
    public function getFields()
    {
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
                $fieldConfig = $field;
                if (empty($fieldConfig['class'])) {
                    $class = Field::class;
                } else {
                    $class = $fieldConfig['class'];
                    unset($fieldConfig['class']);
                }

                if (!is_subclass_of($class, Field::class) && $class !== Field::class) {
                    $this->throwFieldClassException($key, $class);
                }

                $field = \yii::$container->get($class, [$fieldConfig]);
            } else {
                if (!($field instanceof Field)) {
                    $this->throwFieldClassException($key, $field);
                }
            }

            $field->defaultScenario = $this->defaultScenario;
            $field->model = $this->owner;
            if ($field->module === null) {
                $field->module = $this->getModule();
            }

            $field->attach();

            $fields[$key] = $field;
        }

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

        $this->_fields = $fields;
        $this->fieldsIsInitied = true;

        return $fields;
    }

    /**
     * Returns columns configuration for GridView
     * @see \yii\grid\GridView
     * @return array
     * @throws Exception
     */
    public function getGridColumns()
    {
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

    /**
     * Returns attributes configuration for DetailView
     * @see DetailView
     * @return array
     * @throws Exception
     */
    public function getFormFields()
    {
        $formFields = [];
        foreach ($this->getFields() as $fieldKey => $field) {
            $formFields = ArrayHelper::merge($formFields, $field->getFields());
        }

        return $formFields;
    }

    /**
     * Return scopes values
     * @return array
     */
    public function getScopes()
    {
        $scopes = $this->scopes;

        return $scopes;
    }

    /**
     * Sets query directly
     * @param $q
     * @return $this
     */
    public function setQuery($q)
    {
        $this->query = $q;

        return $this;
    }

    /**
     * Get query from model
     * @return ActiveQueryInterface
     */
    public function getQuery()
    {
        if ($this->query === null) {
            $modelClass = get_class($this->owner);
            $this->query = $modelClass::find();
        }

        return $this->query;
    }

    /**
     * Apply query scopes from configuration, fields and plugins
     * @param ActiveQueryInterface $query
     * @return ActiveQueryInterface
     * @throws Exception
     */
    public function applyScopes($query)
    {
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

    /**
     * Returns DataProvider for GridView
     * @see GridView
     * @return ActiveDataProvider
     * @throws Exception
     */
    public function search()
    {
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

    /**
     * Returns model validation rules from configuration, fields and plugins
     * @return array
     * @throws Exception
     */
    public function rules()
    {
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

        foreach ($this->getRelationsNames() as $relation) {
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

    /**
     * Sets module id for message translations
     * @param string $module
     * @return $this
     */
    public function setModule($module)
    {
        $this->_module = $module;

        return $this;
    }

    /**
     * Get module id. If the module id is not specified, then it is calculated from the model namespace
     * @return string|null
     */
    public function getModule()
    {
        if ($this->_module === null) {
            return $this->detectModule();
        }

        return $this->_module;
    }

    /**
     * Returns model attributes labels
     * @return array
     * @throws Exception
     */
    public function attributesLabels()
    {
        $result = [];
        foreach ($this->getFields() as $field) {
            $result[$field->getName()] = $field->getLabel($this->getModule());
        }

        return $result;
    }

    /**
     * Returns MultipleInput model columns
     * @see MultipleInput
     * @return array
     * @throws Exception
     */
    public function getMultipleInputFields()
    {
        $columns = [];
        foreach ($this->getFields() as $key => $field) {
            $field = $field->getMultipleInputField();
            if ($field !== false) {
                $columns[$key] = $field;
            }
        }

        return $columns;
    }

    /**
     * Returns plugins relation by name
     * @param string $name Relation name
     * @return array
     * @throws Exception
     */
    public function getPluginsRelation($name)
    {
        foreach ($this->getPlugins() as $plugin) {
            if ($relation = $plugin->getRelationQuery($name)) {
                return $relation;
            }
        }

        return null;
    }

    /**
     * Checks for a relation by name
     * @param string $name Relation name
     * @return bool
     */
    public function isHasRelation($name): bool
    {
        return in_array($name, $this->getRelationsNames());
    }

    /**
     * Calculate module id by namespace
     * @return mixed|string|void
     */
    protected function detectModule()
    {
        if (!$this->owner) {
            return;
        }

        $ownerClass = get_class($this->owner);
        $parts = explode('\\', $ownerClass);
        if (!empty($parts[1])) {
            return $parts[1];
        }
    }

    /**
     * Fired initDataProvider for plugins
     * @param $dataProvider
     * @throws Exception
     */
    protected function initDataProvider($dataProvider)
    {
        foreach ($this->getPlugins() as $plugin) {
            $plugin->initDataProvider($dataProvider);
        }
    }

    /**
     * Plugins initialization
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    protected function initPlugins()
    {
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
     * Save relations behavior initialization
     */
    protected function initSaverBehaviorRelations()
    {
        $relations = $this->getRelationsNames();
        if (!empty($relations)) {
            $owner = $this->owner;
            $relationsSaver = $owner->getBehavior(self::RELATIONS_SAVER_KEY);
            if (!$relationsSaver) {
                $owner->attachBehavior(self::RELATIONS_SAVER_KEY, [
                    'class' => SaveRelationsBehavior::class,
                ]);
                $relationsSaver = $owner->getBehavior(self::RELATIONS_SAVER_KEY);
            }

            foreach ($relations as $relation) {
                if (!in_array($relation, $relationsSaver->relations)) {
                    $params = [];
                    $relationsSaver->addRelation($relation, $params);
                }
            }
        }
    }

    /**
     * Returns relations names
     * @return array
     * @throws Exception
     */
    protected function getRelationsNames()
    {
        $relationsNames = [];
        foreach ($this->getFields() as $field) {
            if ($name = $field->getRelationName()) {
                $relationsNames[$name] = $name;
            }
        }

        $relationsNames = ArrayHelper::merge($relationsNames, $this->getPluginsRelationsNames());

        return $relationsNames;
    }

    /**
     * Returns plugins relations names
     * @return array
     * @throws Exception
     */
    protected function getPluginsRelationsNames()
    {
        $relationsNames = [];
        foreach ($this->getPlugins() as $plugin) {
            $relationsNames = ArrayHelper::merge($relationsNames, $plugin->getRelationsNames());
        }
        return $relationsNames;
    }

    /**
     * Returns plugins fields
     * @return array
     * @throws Exception
     */
    protected function getPluginsFields()
    {
        $result = [];
        foreach ($this->getPlugins() as $plugin) {
            $result = array_merge($result, $plugin->getFields());
        }

        return $result;
    }

    /**
     * Throw field class exception
     * @param string $key Field name
     * @param $field Field instance
     * @throws Exception
     */
    protected function throwFieldClassException($key, $field): void
    {
        if (!is_string($field)) {
            $field = get_class($field);
        }

        throw new Exception('Field "' . $key . '" must bee instance of ' . Field::class . '. Instance of ' . $field . ' instead');
    }

    /**
     * Returns fields config from configuration and plugins
     * @return array
     * @throws Exception
     */
    protected function getFieldsConfig(): array
    {
        $fields = $this->_fieldsConfig;
        $fields = array_merge($fields, $this->getPluginsFields());

        return $fields;
    }
}
