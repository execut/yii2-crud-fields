<?php
/**
 */

namespace execut\crudFields\fields;

use execut\crudFields\TestCase;
use iutbay\yii2kcfinder\CKEditor;
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
                    'language' => null
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
            'label' => 'Text',
        ], $field->getColumn());
    }

    public function testGetEmptyColumn() {
        $model = new Model();
        $field = new Editor([
            'attribute' => 'name',
            'model' => $model,
            'column' => false,
        ]);
        $this->assertFalse($field->getColumn());
    }
}