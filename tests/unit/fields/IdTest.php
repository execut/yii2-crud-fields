<?php
/**
 */

namespace execut\crudFields\fields;


use execut\crudFields\TestCase;
use kartik\detail\DetailView;

class IdTest extends TestCase
{
    public function testGetField() {
        $model = new FieldTestModel();
        $field = new Id([
            'attribute' => 'name',
            'model' => $model
        ]);
        $this->assertEquals([
            'attribute' => 'name',
            'displayOnly' => true,
            'viewModel' => $model,
            'editModel' => $model,
        ], $field->getField());
    }

    public function testDefaultId() {
        $field = new Id();
        $this->assertEquals('id', $field->attribute);
    }
}