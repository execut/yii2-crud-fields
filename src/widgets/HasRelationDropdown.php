<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 10/1/19
 * Time: 4:15 PM
 */

namespace execut\crudFields\widgets;

use execut\yii\jui\Widget;
use yii\helpers\Html;

class HasRelationDropdown extends Widget
{
    public $model;
    public $attribute;
    public $parentId = null;
    public function run()
    {
        $this->clientOptions['parentSelector'] = '#' . $this->parentId;
        $this->registerWidget();
        $attribute = $this->attribute;
        return $this->_renderContainer(Html::activeDropDownList($this->model, $attribute, [
            '' => '',
            '0' => 'Нет',
            '1' => 'Есть',
        ], [
            'class' => 'inored-input',
        ]));
    }
}