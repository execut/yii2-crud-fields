<?php


namespace execut\crudFields\fields;


use execut\crudFields\TestCase;

class RelationValueTest extends TestCase
{
    public function testGetColumn() {
        $field = new RelationValue;
        $field->setLabel('test');
        $column = $field->getColumn();
        $this->assertArrayHasKey('value', $column);
        $this->assertIsCallable($column['value']);
        $this->assertArrayHasKey('label', $column);
        $this->assertEquals('test', $column['label']);
    }

    public function testGetColumnValueFromAttribute() {
        $model = new \stdClass();
        $model->test_attribute = 'test';
        $field = new RelationValue([
            'model' => $model,
            'attribute' => 'test_attribute',
        ]);

        $column = $field->getColumn();
        $value = $column['value'];
        $value = $value($model);
        $this->assertEquals('test', $value);
    }

    public function testGetColumnValueFromAttributeOfAttribute() {
        $model = new \stdClass();
        $subModel = new \stdClass();
        $subModel->test_attribute = 'test';
        $model->submodel_attribute = $subModel;
        $field = new RelationValue([
            'attribute' => 'submodel_attribute.test_attribute',
        ]);

        $column = $field->getColumn();
        $value = $column['value'];
        $value = $value($model);
        $this->assertEquals('test', $value);
    }

    public function testGetColumnValueFromArray() {
        $model = new \stdClass();
        $model->test_attribute = ['test1', 'test2'];
        $field = new RelationValue([
            'attribute' => 'test_attribute',
        ]);

        $column = $field->getColumn();
        $value = $column['value'];
        $value = $value($model);
        $this->assertEquals('test1, test2', $value);
    }

    public function testGetColumnValueFromArrayOfModels() {
        $model = new \stdClass();
        $subModel = new \stdClass();
        $subModel->test_attribute = 'test';
        $model->submodel_attribute = [$subModel, $subModel];
        $field = new RelationValue([
            'attribute' => 'submodel_attribute.test_attribute',
        ]);

        $column = $field->getColumn();
        $value = $column['value'];
        $value = $value($model);
        $this->assertEquals('test, test', $value);
    }

    public function testGetColumnValueOfModelFromArrayOfModels() {
        $model = new \stdClass();
        $subSubModel = new \stdClass();
        $subSubModel->test_attribute = 'test';

        $subModel = new \stdClass();
        $subModel->subsubmodel_attribute = $subSubModel;

        $model->submodel_attribute = [$subModel, $subModel];
        $field = new RelationValue([
            'attribute' => 'submodel_attribute.subsubmodel_attribute.test_attribute',
        ]);

        $column = $field->getColumn();
        $value = $column['value'];
        $value = $value($model);
        $this->assertEquals('test, test', $value);
    }

    public function testNoScope() {
        $field = new RelationValue();
        $this->assertFalse($field->scope);
    }

    public function testNoField() {
        $field = new RelationValue();
        $this->assertFalse($field->getField());
    }

    public function testSkipEmptyValues() {
        $model = new \stdClass();
        $model->test_attribute = ['test', ''];

        $field = new RelationValue([
            'attribute' => 'test_attribute',
        ]);

        $column = $field->getColumn();
        $value = $column['value'];
        $value = $value($model);
        $this->assertEquals('test', $value);
    }
}