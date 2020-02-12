<?php
/**
 */

namespace execut\crudFields\fields;


use execut\crudFields\Relation;
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

    public function testGetColumns() {
        $column = [
            'class' => 'test',
        ];
        $field = new Field([
            'column' => $column,
            'attribute' => 'name'
        ]);
        $this->assertEquals([
            'name' => [
                'class' => 'test',
                'attribute' => 'name',
                'label' => 'Name'
            ],
        ], $field->columns);
    }

    public function testGetFields() {
        $formField = [
            'class' => 'test'
        ];
        $field = new Field([
            'field' => $formField,
            'attribute' => 'name'
        ]);
        $this->assertEquals([
            'name' => [
                'class' => 'test',
                'attribute' => 'name',
            ],
        ], $field->fields);
    }

    public function testApplyScopes() {
        $model = new Model;
        $q = $this->getMockBuilder(ActiveQuery::class)->setMethods(['andWhere'])->setConstructorArgs([
            'modelClass' => $model->className(),
        ]) ->getMock();
        $q->expects($this->once())->method('andWhere')->with([
            'test_model_table.name' => 'test'
        ])->will($this->returnValue($q));

        $field = new Field([
            'model' => $model,
            'attribute' => 'name',
        ]);
        $this->assertEquals($q, $field->applyScopes($q));
    }

    public function testApplyScopesWithNullValue() {
        $model = new Model;
        $q = $this->getMockBuilder(ActiveQuery::class)->setMethods(['andWhere'])->setConstructorArgs([
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
        $q = $this->getMockBuilder(ActiveQuery::class)->setMethods(['andWhere'])->setConstructorArgs([
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

    public function testApplyScopesWithRelationObject() {
        $relation = $this->getMockBuilder(Relation::class)->setMethods(['applyScopes'])->getMock();
        $relation->expects($this->once())->method('applyScopes');
        $field = new Field([
            'relation' => 'test',
            'relationObject' => $relation,
        ]);

        $field->applyScopes(new ActiveQuery('asdas'));
    }

    public function testGetRules() {
        $model = new Model();
        $field = new Field([
            'model' => $model,
            'attribute' => 'name',
        ]);
        $this->assertEquals([
            'nameSafeOnGrid' => [
                ['name'],
                'safe',
                'on' => Field::SCENARIO_GRID,
            ],
            'namesafeonFormAndDefault' => [
                ['name'],
                'safe',
                'on' => [Field::SCENARIO_FORM],
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
            'nameSafeOnGrid' => [
                ['name'],
                'safe',
                'on' => Field::SCENARIO_GRID,
            ],
            'namerequiredonFormAndDefault' => [
                ['name'],
                'required',
                'on' => [Field::SCENARIO_FORM,],
            ],
        ], $field->rules());
    }

    public function testGetRelationFields() {

    }
}

class Model extends ActiveRecord {
    public $id = 2;
    public $name = 'test';
    public $test_test_id = 2;
    public $testTest = null;
    public static $query = null;
    public static $subQuery = null;

    public static function primaryKey() {
        return ['id'];
    }

    public static function find()
    {
        return self::$query;
    }

    public function getTestTest()
    {
        if (self::$subQuery === null) {
            self::$subQuery = new ActiveQuery(Model::class);
        }

        return self::$subQuery;
    }

    public function getRelation($name, $throwException = true)
    {
        if ($name === 'testTest') {
            return $this->getTestTest();
        } else {
            return parent::getRelation($name, $throwException);
        }
    }

    public static function tableName()
    {
        return 'test_model_table';
    }
}