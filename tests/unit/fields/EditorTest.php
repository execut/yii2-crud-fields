<?php
/**
 */

namespace execut\crudFields\fields;


use dosamigos\ckeditor\CKEditor;
use execut\crudFields\TestCase;
use kartik\detail\DetailView;
use kartik\grid\GridView;
use yii\i18n\Formatter;

class EditorTest extends TestCase
{
    public function testGetField() {
        $field = new Editor([
            'attribute' => 'text',
        ]);
        $this->assertEquals([
            'attribute' => 'text',
            'type' => DetailView::INPUT_WIDGET,
            'format' => 'html',
            'widgetOptions' => [
                'class' => CKEditor::class,
                'preset' => 'full',
                'clientOptions' => [
                    'allowedContent' => true,
                ],
            ],
        ], $field->getField());
    }

    public function testGetColumn() {
        $field = new Editor([
            'attribute' => 'text',
        ]);
        $this->assertEquals([
            'attribute' => 'text',
            'value' => function () {},
        ], $field->getColumn());
    }
}