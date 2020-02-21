<?php
/**
 */

namespace execut\crudFields\fields;


use execut\crudFields\TestCase;
use kartik\detail\DetailView;
use kartik\grid\GridView;
use yii\db\ActiveQuery;
use yii\web\JsExpression;

class HasOneSelect2Test extends TestCase
{
    public function testGetEmptyMultipleInputField() {
        $field = new HasOneSelect2([
            'multipleInputField' => false,
        ]);
        $this->assertFalse($field->getMultipleInputField());
    }
}