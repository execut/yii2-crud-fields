<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 12/26/17
 * Time: 10:46 AM
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