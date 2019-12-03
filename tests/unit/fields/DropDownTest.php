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
use unclead\multipleinput\MultipleInputColumn;
use yii\db\ActiveQuery;

class DropDownTest extends TestCase
{
    public function testGetField() {
        $fieldObject = $this->getField();
        $field = $fieldObject->getField();
        $this->assertEquals([
            'type'=> DetailView::INPUT_DROPDOWN_LIST,
            'attribute' => 'test_test_id',
            'value' => function() {},
            'items' => [
                '' => '',
                2 => 'test',
            ],
            'viewModel' => $fieldObject->model,
            'editModel' => $fieldObject->model,
        ], $field);
    }

    public function testGetMultipleInputField() {
        $field = $this->getField();
        $field = $field->getMultipleInputField();
        $this->assertEquals([
            'type'=> MultipleInputColumn::TYPE_DROPDOWN,
            'name' => 'test_test_id',
            'enableError' => true,
            'items' => [
                '' => '',
                2 => 'test',
            ],
            'options' => [
                'placeholder' => 'Test Test',
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
     * @return Field
     */
    protected function getField()
    {
        $model = new Model();
        $model->testTest = $model;

        $field = new DropDown([
            'attribute' => 'test_test_id',
            'relation' => 'testTest',
            'model' => $model,
            'valueAttribute' => 'name',
        ]);

        $query = Model::$query = $this->getMockBuilder(ActiveQuery::class)
            ->setConstructorArgs([Model::class])
            ->setMethods(['andWhere', 'all'])
            ->getMock();
        $query->method('andWhere')->with(['id' => [2]])->willReturn($query);
        $query->method('all')->willReturn([$field->model]);
        Model::$subQuery = clone Model::$query;
        Model::$subQuery->link = [
            'id' => 'test_test_id',
        ];

        return $field;
    }
}