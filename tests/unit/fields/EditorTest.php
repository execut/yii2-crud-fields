<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

namespace execut\crudFields\fields;

use execut\crudFields\TestCase;
use iutbay\yii2kcfinder\CKEditor;
use kartik\detail\DetailView;
use kartik\grid\GridView;
use yii\i18n\Formatter;

class EditorTest extends TestCase
{
    public function testGetDetailViewField() {
        $field = new Editor();
        $this->assertInstanceOf(\execut\crudFields\fields\detailViewField\Editor::class, $field->getDetailViewField());
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
        $model = new FieldTestModel();
        $field = new Editor([
            'attribute' => 'name',
            'model' => $model,
            'column' => false,
        ]);
        $this->assertFalse($field->getColumn());
    }
}