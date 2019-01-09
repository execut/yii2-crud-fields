<?php
namespace execut\crudFields;


use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

abstract class Plugin
{
    /**
     * @var Behavior
     */
    public $owner = null;
    public $fields = [];
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

    public function afterSave() {
    }

    public function beforeDelete() {
    }
}