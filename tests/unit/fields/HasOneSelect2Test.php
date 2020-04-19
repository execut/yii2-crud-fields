<?php
/**
 */

namespace execut\crudFields\fields;


use Codeception\Test\Unit;
use execut\crudFields\Relation;
use kartik\detail\DetailView;
use kartik\grid\GridView;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\JsExpression;

class HasOneSelect2Test extends Unit
{
    public function testGetField() {
        $relationObject = $this->getMockBuilder(Relation::class)->getMock();
        $model = new HasOneSelect2TestModel;
        $field = new HasOneSelect2([
            'relationObject' => $relationObject,
            'attribute' => 'name',
            'model' => $model,
        ]);
        $field = $field->getField();
        $this->assertArrayHasKey('type', $field);
    }

    public function testGetColumn() {
        $relationObject = $this->getMockBuilder(Relation::class)->onlyMethods(['getSourcesText', 'getRelationFormName', 'getRelatedModels', 'getData'])->getMock();
        $relationObject->method('getSourcesText')
            ->willReturn([]);
        $relationObject->method('getRelationFormName')
            ->willReturn('test');
        $relationObject->method('getRelatedModels')
            ->willReturn([]);
        $relationObject->method('getData')
            ->willReturn([]);
        $relationObject->url = [
            '/test/test',
        ];
        $model = new HasOneSelect2TestModel;
        $field = new HasOneSelect2([
            'relationObject' => $relationObject,
            'attribute' => 'name',
            'model' => $model,
        ]);
        $field = $field->getColumn();
        $this->assertArrayHasKey('attribute', $field);
    }

    public function testGetEmptyMultipleInputField() {
        $field = new HasOneSelect2([
            'multipleInputField' => false,
        ]);
        $this->assertFalse($field->getMultipleInputField());
    }
}

class HasOneSelect2TestModel extends FieldTestModel {
}