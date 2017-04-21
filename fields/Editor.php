<?php
/**
 */

namespace execut\crudFields\fields;


use dosamigos\ckeditor\CKEditor;
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
                    'allowedContent' => true,
                ],
            ],
        ]);
    }

    public function getColumn()
    {
        return array_merge(parent::getColumn(), [
            'format' => 'html',
        ]);
    }
}