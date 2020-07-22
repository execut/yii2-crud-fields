<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields;

use Codeception\Test\Unit;
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
}

class HasOneSelect2TestModel extends FieldTestModel
{
}
