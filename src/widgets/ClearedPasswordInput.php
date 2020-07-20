<?php


namespace execut\crudFields\widgets;

use execut\yii\jui\InputWidget;
use kartik\password\PasswordInput;
use yii\helpers\ArrayHelper;

class ClearedPasswordInput extends InputWidget
{
    public $widgetClass = PasswordInput::class;
    public $widgetOptions = [];
    public function run()
    {
        $attribite = $this->attribute;
        if (!$this->model->$attribite) {
            $this->registerWidget();
        }

        $inputWidget = $this->widgetClass;
        $widgetOptions = $this->widgetOptions;
        $widgetOptions['id'] = $this->id;
        $widgetOptions['model'] = $this->model;
        $widgetOptions['attribute'] = $this->attribute;
        $widgetOptions = ArrayHelper::merge($widgetOptions, [
            'pluginOptions' => [
                'inputTemplate' => '{input}',
            ],
        ]);

        return $inputWidget::widget($widgetOptions);
    }
}