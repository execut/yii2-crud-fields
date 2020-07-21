<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\widgets;

use execut\crudFields\fields\HasOneRadioList;
use execut\yii\jui\WidgetTrait;
use yii\helpers\Html;
use yii\jui\InputWidget;

/**
 * Widget for switching visibility the relation subform inside DetailView. For HasOneRadioList
 * @package execut\crudFields\widgets
 * @see HasOneRadioList
 */
class RadioListWithSubform extends InputWidget
{
    use WidgetTrait;

    /**
     * @var array Radio items list
     */
    public $data = [];

    /**
     * {@inheritDoc}
     */
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
