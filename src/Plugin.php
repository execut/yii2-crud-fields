<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields;

use yii\base\BaseObject;
use yii\data\DataProviderInterface;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * Class Plugin
 * @package execut\crudFields
 */
abstract class Plugin extends BaseObject
{
    /**
     * @var Behavior
     */
    public $owner = null;
    public $fields = [];
    public $rules = [];
    /**
     * @var QueryFromConfigFactory
     */
    public $factory = null;
    public function getFields()
    {
        return ArrayHelper::merge($this->_getFields(), $this->fields);
    }

    protected function _getFields()
    {
        return [];
    }

    /**
     * Example:
     * [
     *    [
     *      'class' => Page::class,
     *      'name' => 'pagesPage',
     *      'link' => [
     *          'id' => 'pages_page_id',
     *      ],
     *      'multiple' => false
     *    ]
     * ]
     *
     * @return array
     */
    public function getRelations()
    {
        return [];
    }

    public function getFactory()
    {
        if ($this->factory === null) {
            $this->factory = new QueryFromConfigFactory;
        }

        return $this->factory;
    }

    protected $queries = [];
    public function getRelationQuery($name)
    {
        $queries = &$this->queries;
        if (array_key_exists($name, $queries)) {
            return $queries[$name];
        }
        $factory = $this->getFactory();
        $relations = $this->getRelations();
        if (!empty($relations[$name])) {
            $factory->setModel($this->owner);
            $factory->setParams($relations[$name]);
            $queries[$name] = $factory->create();
        } else {
            $queries[$name] = false;
        }

        return $queries[$name];
    }

    public function getRelationsNames()
    {
        return array_keys($this->getRelations());
    }

    public function rules()
    {
        return ArrayHelper::merge($this->rules, $this->_rules());
    }

    protected function _rules()
    {
        return [];
    }

    public function initDataProvider(DataProviderInterface $dataProvider)
    {
    }

    public function applyScopes(ActiveQuery $q)
    {
    }

    public function attach()
    {
    }

    public function afterUpdate()
    {
    }

    public function afterInsert()
    {
    }

    public function beforeValidate()
    {
    }

    public function afterValidate()
    {
    }

    public function beforeUpdate()
    {
    }

    public function beforeInsert()
    {
    }

    public function beforeSave()
    {
    }

    public function afterSave()
    {
    }

    public function beforeDelete()
    {
    }

    public function afterLoad()
    {
    }
}
