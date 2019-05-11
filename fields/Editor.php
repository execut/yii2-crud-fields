<?php
/**
 */

namespace execut\crudFields\fields;

use iutbay\yii2kcfinder\CKEditor;
use kartik\detail\DetailView;
class Editor extends Field
{
    public function getField()
    {
        return array_merge(parent::getField(), [
            'type' => DetailView::INPUT_WIDGET,
            'format' => 'html',
            'widgetOptions' => [
                'class' => CKEditor::class,
                'preset' => 'full',
                'clientOptions' => [
                    'language' => \yii::$app->language,
                    'allowedContent' => true,
                ],
            ],
        ]);
    }

    public function getColumn()
    {
        return array_merge(parent::getColumn(), [
            'value' => function ($row) {
                $attribute = $this->attribute;

                return substr(strip_tags($row->$attribute), 0, 60);
            },
        ]);
    }
}