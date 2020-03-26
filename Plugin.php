<?php
namespace execut\crudFields;


use yii\base\BaseObject;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

abstract class Plugin extends BaseObject
{
    /**
     * @var Behavior
     */
    public $owner = null;
    public $fields = [];
    public $rules = [];
    public function getFields() {
        return ArrayHelper::merge($this->_getFields(), $this->fields);
    }

    protected function _getFields() {
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
    public function getRelations() {
        return [];
    }

    public function rules() {
        return ArrayHelper::merge($this->rules, $this->_rules());
    }

    protected function _rules() {
        return [];
    }

    public function initDataProvider($dataProvider) {
    }

    public function applyScopes(ActiveQuery $q) {
    }

    public function attach() {
    }

    public function afterUpdate() {
    }

    public function afterInsert() {
    }

    public function beforeValidate() {
    }

    public function beforeUpdate() {
    }

    public function beforeInsert() {
    }

    public function beforeSave() {
    }

    public function afterSave() {
    }

    public function beforeDelete() {
    }
}