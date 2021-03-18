<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\tests\unit\fields;

use Codeception\Test\Unit;
use execut\crudFields\fields\HasOneSelect2;
use execut\crudFields\Relation;

class HasOneSelect2Test extends Unit
{
    public function testGetField()
    {
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

    public function testGetFieldWithoutRelation()
    {
        $model = new HasOneSelect2TestModel;
        $data = ['test' => 'test'];
        $field = new HasOneSelect2([
            'attribute' => 'name',
            'model' => $model,
            'data' => $data,
        ]);
        $field = $field->getField();
        $this->assertArrayHasKey('widgetOptions', $field);
        $this->assertArrayHasKey('data', $field['widgetOptions']);
        $this->assertEquals($data, $field['widgetOptions']['data']);
    }

    public function testGetColumn()
    {
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

    public function testGetEmptyMultipleInputField()
    {
        $field = new HasOneSelect2([
            'multipleInputField' => false,
        ]);
        $this->assertFalse($field->getMultipleInputField());
    }

    public function testGetMultipleInputFieldWithData()
    {
        $model = new HasOneSelect2TestModel;
        $data = ['test' => 'test'];
        $field = new HasOneSelect2([
            'attribute' => 'name',
            'model' => $model,
            'data' => $data,
        ]);
        $field = $field->getMultipleInputField();
        $this->assertIsArray($field);
    }

    public function testGetMultipleInputFieldWithRelationModels()
    {
        $model = new HasOneSelect2TestModel;
        $field = new HasOneSelect2([
            'attribute' => 'name',
            'model' => $model,
            'relation' => 'testTest',
        ]);

        $relationModel = new HasOneSelect2TestModel();
        $subRelationModel = new HasOneSelect2TestModel();
        $subRelationName = 'sub relation name';
        $subRelationModel->name = $subRelationName;
        $relationModel->testTest = $subRelationModel;
        $relationModels = [
            $relationModel
        ];
        $multipleInputField = $field->getMultipleInputField($relationModels);
        $this->assertIsArray($multipleInputField);
        $this->assertArrayHasKey('options', $multipleInputField);
        $options = $multipleInputField['options'];
        $this->assertArrayHasKey('data', $options);
        $this->assertEquals([
            2 => $subRelationName,
        ], $options['initValueText']);
    }

    public function testGetMultipleInputFieldWithRelationModelsException()
    {
        $field = new HasOneSelect2([
            'attribute' => 'name',
        ]);
        $this->expectExceptionMessage('Relation name is required for generation select2 widget options for field name');
        $multipleInputField = $field->getMultipleInputField([1]);
    }

    public function testGetMultipleInputFieldWithRelationModelsWhenHasDataOption()
    {
        $data = [
            'test',
        ];
        $field = new HasOneSelect2([
            'attribute' => 'name',
            'data' => $data
        ]);
        $multipleInputField = $field->getMultipleInputField([1]);
        $this->assertIsArray($multipleInputField);
        $this->assertArrayHasKey('options', $multipleInputField);
        $options = $multipleInputField['options'];
        $this->assertArrayHasKey('data', $options);
        $this->assertEquals($data, $options['data']);
    }
}

class HasOneSelect2TestModel extends FieldTestModel
{
}
