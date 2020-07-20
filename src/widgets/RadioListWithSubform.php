<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 4/17/18
 * Time: 2:51 PM
 */

namespace execut\crudFields\widgets;

use execut\yii\jui\WidgetTrait;
use yii\helpers\Html;
use yii\jui\InputWidget;

class RadioListWithSubform extends InputWidget
{
    use WidgetTrait;
    public $data = [];
    public function run()
    {
        $data = array_filter($this->data);
        $this->_registerBundle();
        $this->registerWidget();

        return $this->_renderContainer(Html::activeRadioList($this->model, $this->attribute, $data, [
            'encode' => false,
            'separator' => '<br>',
        ]));
    }
}