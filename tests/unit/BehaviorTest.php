<?php
/**
 */

namespace execut\crudFields;


use execut\crudFields\Behavior;
use execut\crudFields\fields\Field;
use PHPUnit\Framework\TestCase;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

class BehaviorTest extends TestCase
{
    public function testSetFields() {
        $model = new Model;
        $behavior = new Behavior([
            'owner' => $model,
            'fields' => [
                [
                    'attribute' => 'name',
                ],
            ],
        ]);
        $fields = $behavior->fields;
        $this->assertArrayHasKey(0, $fields);
        $field = $fields[0];
        $this->assertInstanceOf(Field::class, $field);
        $this->assertEquals($model, $field->model);
    }

    public function testSetFieldsByAttribute() {
        $model = new Model;
        $behavior = new Behavior([
            'owner' => $model,
            'fields' => [
                'name',
            ],
        ]);
        $fields = $behavior->fields;
        $this->assertCount(1, $fields);
    }

    public function testSetFieldsByClass() {
        $model = new Model;
        $behavior = new Behavior([
            'owner' => $model,
            'fields' => [
                Field::class,
            ],
        ]);
        $fields = $behavior->fields;
        $this->assertCount(1, $fields);
        $this->assertNull($fields[0]->attribute);
    }

    public function testGetGridColumns() {
        $field = $this->getMockBuilder(Field::class)->setMethods(['getColumn'])->getMock();
        $fieldConfig = [
            'test' => 'test',
        ];
        $field->expects($this->once())->method('getColumn')->willReturn($fieldConfig);
        $behavior = new Behavior([
            'fields' => [
                $field
            ],
        ]);
        $this->assertEquals([
            [
                'test' => 'test'
            ]
        ], $behavior->getGridColumns());
    }

    public function testGetFormFields() {
        $field = $this->getMockBuilder(Field::class)->setMethods(['getField'])->getMock();
        $fieldConfig = [
            'test' => 'test',
        ];
        $field->expects($this->once())->method('getField')->willReturn($fieldConfig);
        $behavior = new Behavior([
            'fields' => [
                $field
            ],
        ]);
        $this->assertEquals([
            [
                'test' => 'test'
            ]
        ], $behavior->getFormFields());
    }

    public function testApplyScopes() {
        $field = $this->getMockBuilder(Field::class)->setMethods(['applyScopes'])->getMock();
        $model = new Model;
        $q = $this->getMockBuilder(ActiveQuery::className())->setMethods(['andWhere'])->setConstructorArgs([
            'modelClass' => $model->className(),
        ]) ->getMock();

        $field->expects($this->once())->method('applyScopes')->with($q)->willReturn($q);
        $behavior = new Behavior([
            'fields' => [
                $field
            ],
        ]);
        $result = $behavior->applyScopes($q);
        $this->assertEquals($q, $result);
    }

    public function testSearch() {
        $behavior = $this->getMockBuilder(Behavior::class)->setMethods(['applyScopes'])->getMock();
        $model = new Model();
        $behavior->owner = $model;
        $q = new ActiveQuery([
            'modelClass' => $model::className(),
        ]);

        $model::$query = $q;
        $behavior->method('applyScopes')->with($model::$query)->willReturn($model::$query);
        $result = $behavior->search();
        $this->assertInstanceOf(ActiveDataProvider::className(), $result);
        $this->assertEquals($q, $result->query);
    }

    public function testRules() {
        $field = $this->getMockBuilder(Field::class)->setMethods(['rules'])->getMock();
        $fieldConfig = [
            [
                'test' => 'test',
            ],
        ];
        $field->expects($this->once())->method('rules')->willReturn($fieldConfig);
        $behavior = new Behavior([
            'fields' => [
                $field
            ],
        ]);
        $this->assertEquals([
            [
                'test' => 'test'
            ]
        ], $behavior->rules());
    }

    public function testReturnFalse() {
        $field = $this->getMockBuilder(Field::class)->setMethods(['rules', 'getField', 'getColumn'])->getMock();
        $field->expects($this->once())->method('rules')->willReturn(false);
        $field->expects($this->once())->method('getField')->willReturn(false);
        $field->expects($this->once())->method('getColumn')->willReturn(false);
        $behavior = new Behavior([
            'fields' => [
                $field
            ],
        ]);
        $this->assertEquals([], $behavior->rules());
        $this->assertEquals([], $behavior->getFormFields());
        $this->assertEquals([], $behavior->getGridColumns());
    }
}

class Model extends ActiveRecord {
    public static $query = null;
    public static function find()
    {
        return self::$query;
    }
}