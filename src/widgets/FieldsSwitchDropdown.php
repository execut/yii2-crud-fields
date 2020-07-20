<?php


namespace execut\crudFields\widgets;


use execut\yii\jui\Widget;
use yii\helpers\Html;
use yii\jui\InputWidget;

class FieldsSwitchDropdown extends Widget
{
    public $model;
    public $attribute;
    public $data = [];
    public function run()
    {
        $this->registerWidget();
        $attribute = $this->attribute;
        return $this->_renderContainer(Html::activeDropDownList($this->model, $attribute, $this->data));
    }
}