<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
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