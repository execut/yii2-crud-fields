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

/**
 * Field for rendering autoresize textarea CRUD field based on \execut\autosizeTextarea\TextareaWidget
 * @see \execut\autosizeTextarea\TextareaWidget
 * @package execut\crudFields
 */
class AutosizeTextarea extends StringField
{
    /**
     * Unlimited length
     * {@inheritdoc}
     */
    public $maxLength = false;

    /**
     * {@inheritdoc}
     */
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
