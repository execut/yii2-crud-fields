<?php
/**
 */

namespace execut\crudFields\fields\detailViewField;

use execut\crudFields\TestCase;
use iutbay\yii2kcfinder\CKEditor;
use kartik\detail\DetailView;

class EditorTest extends TestCase
{
    public function testGetConfig() {
        $field = new Editor([], 'text');
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
        ], $field->getConfig());
    }
}