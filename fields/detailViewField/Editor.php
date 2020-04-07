<?php
/**
 */

namespace execut\crudFields\fields\detailViewField;

use execut\crudFields\fields\DetailViewField;
use iutbay\yii2kcfinder\CKEditor;
use kartik\detail\DetailView;

class Editor extends DetailViewField
{
    public function getConfig($model = null)
    {
        return array_merge(parent::getConfig($model), [
            'type' => DetailView::INPUT_WIDGET,
            'format' => 'html',
            'widgetOptions' => [
                'class' => CKEditor::class,
                'preset' => 'full',
                'clientOptions' => [
                    'language' => \yii::$app ? \yii::$app->language : null,
                    'allowedContent' => true,
                ],
            ],
        ]);
    }
}