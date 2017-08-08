<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 6/27/17
 * Time: 5:28 PM
 */

namespace execut\crudFields\fields;


use execut\crudFields\TestCase;
use kartik\detail\DetailView;
use yii\db\ActiveQuery;

class DropDownTest extends TestCase
{
    public function testGetField() {
        $field = $this->getField();
        $field = $field->getField();
        $this->assertEquals([
            'type'=> DetailView::INPUT_DROPDOWN_LIST,
            'attribute' => 'test_test_id',
            'value' => function() {},
            'items' => [
                '' => '',
                2 => 'test',
            ],
            'options' => [
                'prompt' => '',
            ],
        ], $field);
    }

    public function testGetColumn() {
        $field = $this->getField();

        $this->assertEquals([
            'attribute' => 'test_test_id',
            'value' => 'name',
            'filter' => [
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

        $field = new DropDown([
            'attribute' => 'test_test_id',
            'model' => $model,
            'valueAttribute' => 'name',
        ]);

        $query = Model::$query = Model::$subQuery = $this->getMockBuilder(ActiveQuery::className())
            ->setConstructorArgs([Model::className()])
            ->setMethods(['andWhere', 'all'])
            ->getMock();
        $query->method('andWhere')->with(['id' => [2]])->willReturn($query);
        $query->method('all')->willReturn([$field->model]);

        return $field;
    }
}