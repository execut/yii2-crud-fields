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
                    'language' => \yii::$app ? \yii::$app->language : null,
                    'allowedContent' => true,
                ],
            ],
        ]);
    }

    public function getColumn()
    {
        $column = parent::getColumn();
        if ($column === false) {
            return $column;
        }

        return array_merge($column, [
            'value' => function ($row) {
                $attribute = $this->attribute;

                return substr(strip_tags($row->$attribute), 0, 60);
            },
        ]);
    }
}