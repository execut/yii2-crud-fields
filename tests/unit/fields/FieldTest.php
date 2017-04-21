<?php
/**
 */

namespace execut\crudFields\fields;


use execut\crudFields\TestCase;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

class FieldTest extends TestCase
{
    public function testGettersWithoutAttribute() {
        $field = new Field();
        $this->assertEquals([], $field->rules());
        $this->assertEquals([], $field->getColumn());
        $this->assertEquals([], $field->getField());
        $query = $this->getMockBuilder(ActiveQuery::class)->setConstructorArgs([Model::class])->getMock();
        $this->assertEquals($query, $field->applyScopes($query));
    }

    public function testGetColumn() {
        $column = [
            'class' => 'test',
        ];
        $field = new Field([
            'column' => $column,
            'attribute' => 'name'
        ]);
        $this->assertEquals([
            'class' => 'test',
            'attribute' => 'name'
        ], $field->column);
    }

    public function testGetField() {
        $formField = [
            'class' => 'test'
        ];
        $field = new Field([
            'field' => $formField,
            'attribute' => 'name'
        ]);
        $this->assertEquals([
            'class' => 'test',
            'attribute' => 'name'
        ], $field->field);
    }

    public function testApplyScopes() {
        $model = new Model;
        $q = $this->getMockBuilder(ActiveQuery::className())->setMethods(['andWhere'])->setConstructorArgs([
            'modelClass' => $model->className(),
        ]) ->getMock();
        $q->expects($this->once())->method('andWhere')->with([
            'name' => 'test'
        ])->will($this->returnValue($q));

        $field = new Field([
            'model' => $model,
            'attribute' => 'name',
        ]);
        $this->assertEquals($q, $field->applyScopes($q));
    }

    public function testApplyScopesWithNullValue() {
        $model = new Model;
        $q = $this->getMockBuilder(ActiveQuery::className())->setMethods(['andWhere'])->setConstructorArgs([
            'modelClass' => $model->className(),
        ]) ->getMock();
        $q->expects($this->never())->method('andWhere');

        $model->name = null;
        $field = new Field([
            'model' => $model,
            'attribute' => 'name',
        ]);
        $this->assertEquals($q, $field->applyScopes($q));
    }

    public function testApplyScopesWithEmptyValue() {
        $model = new Model;
        $q = $this->getMockBuilder(ActiveQuery::className())->setMethods(['andWhere'])->setConstructorArgs([
            'modelClass' => $model->className(),
        ]) ->getMock();
        $q->expects($this->never())->method('andWhere');

        $model->name = '';
        $field = new Field([
            'model' => $model,
            'attribute' => 'name',
        ]);
        $this->assertEquals($q, $field->applyScopes($q));
    }

    public function testGetRules() {
        $model = new Model();
        $field = new Field([
            'model' => $model,
            'attribute' => 'name',
        ]);
        $this->assertEquals([
            [
                ['name'],
                'safe',
                'on' => Field::SCENARIO_GRID,
            ],
            [
                ['name'],
                'safe',
                'on' => Field::SCENARIO_FORM,
            ],
        ], $field->rules());
    }

    public function testGetRulesWhileRequired() {
        $model = new Model();
        $field = new Field([
            'model' => $model,
            'attribute' => 'name',
            'required' => true,
        ]);
        $this->assertEquals([
            [
                ['name'],
                'safe',
                'on' => Field::SCENARIO_GRID,
            ],
            [
                ['name'],
                'required',
                'on' => Field::SCENARIO_FORM,
            ],
        ], $field->rules());
    }
}

class Model extends ActiveRecord {
    public $id = 2;
    public $name = 'test';
    public $test_test_id = 2;
    public $testTest = null;
    public static $query = null;
    public static $subQuery = null;
    public static function find()
    {
        return self::$query;
    }

    public function getTestTest()
    {
        if (self::$subQuery === null) {
            self::$subQuery = new ActiveQuery(Model::className());
        }

        return self::$subQuery;
    }
}