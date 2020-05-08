<?php


namespace execut\crudFields\fields;


use execut\crudFields\Relation;
use execut\crudFields\TestCase;

class HasManyMultipleInputTest extends TestCase
{
    public function testGetField() {
        $relationObject = $this->getMockBuilder(Relation::class)->getMock();
        $model = new HasOneSelect2TestModel;
        $relationObject->method('getRelationModel')
            ->with(true)
            ->willReturn($model);
        $field = new HasManyMultipleInput([
            'relationObject' => $relationObject,
            'attribute' => 'name',
            'model' => $model,
        ]);
        $field = $field->getField();
        $this->assertArrayHasKey('type', $field);
    }

    public function testApplyScopes() {

    }

    public function testGetColumn() {
        return;
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
        $field = new HasManyMultipleInput([
            'relationObject' => $relationObject,
            'attribute' => 'name',
            'model' => $model,
        ]);
        $field = $field->getColumn();
        $this->assertArrayHasKey('attribute', $field);
    }

    public function testGetEmptyMultipleInputField() {
        $field = new HasManyMultipleInput([
            'multipleInputField' => false,
        ]);
        $this->assertFalse($field->getMultipleInputField());
    }
}