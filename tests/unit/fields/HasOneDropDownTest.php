<?php
/**
 */

namespace execut\crudFields\fields;


use execut\crudFields\TestCase;
use kartik\detail\DetailView;
use kartik\grid\GridView;
use yii\db\ActiveQuery;
use yii\web\JsExpression;

class HasOneDropDownTest extends TestCase
{
    public function testGetField() {
        $field = $this->getField();
        $field = $field->getField();
        $this->assertEquals([
            'attribute' => 'test_test_id',
            'value' => 2,
            'data' => [
                '' => '',
                2 => 'test',
            ],
        ], $field);
    }

    public function testGetColumn() {
        $field = $this->getField();


        $this->assertEquals([
            'attribute' => 'test_test_id',
            'value' => 2,
            'data' => [
                '' => '',
                2 => 'test',
            ],
        ], $field->getColumn());
    }

    /**
     * @return array|HasOneRelation
     */
    protected function getField()
    {
        $model = new Model();
        $model->testTest = $model;

        $field = new HasOneDropDown([
            'attribute' => 'test_test_id',
            'model' => $model,
        ]);

        $query = Model::$query = $this->getMockBuilder(ActiveQuery::className())
            ->setConstructorArgs([Model::className()])
            ->setMethods(['andWhere', 'all'])
            ->getMock();
        $query->method('andWhere')->with(['id' => [2]])->willReturn($query);
        $query->method('all')->willReturn([$field->model]);

        return $field;
    }
}