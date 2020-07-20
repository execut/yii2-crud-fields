<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
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