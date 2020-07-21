<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\widgets;

use execut\yii\jui\Widget;
use yii\base\Model;
use yii\helpers\Html;

/**
 * Widget for rendering has relation filter
 * @package execut\crudFields
 */
class HasRelationDropdown extends Widget
{
    /**
     * @var Model $model Model instance
     */
    public $model;
    /**
     * @var string Is has relation attribute name
     */
    public $attribute;
    /**
     * @var string Parent filter element for disabling
     */
    public $parentId = null;

    /**
     * {@inheritDoc}
     */
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
