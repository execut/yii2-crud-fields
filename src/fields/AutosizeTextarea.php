<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields;
use execut\autosizeTextarea\TextareaWidget;
use kartik\detail\DetailView;
use yii\helpers\ArrayHelper;
class AutosizeTextarea extends StringField
{
    public $maxLength = false;
    public function getField()
    {
        $parentField = parent::getField();
        if ($parentField === false) {
            return false;
        }

        $field = [
            'type' => DetailView::INPUT_WIDGET,
            'options' => [
                'class' => 'form-control',
                'style' => 'height: 32px',
            ],
            'widgetOptions' => [
                'class' => TextareaWidget::class,
                'clientOptions' => [
                    'vertical' => true,
                    'horizontal' => false,
                ],
            ],
        ];
        return ArrayHelper::merge($parentField, $field);
    }
}