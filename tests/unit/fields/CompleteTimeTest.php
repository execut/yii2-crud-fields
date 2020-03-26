<?php


namespace execut\crudFields\fields;

use yii\bootstrap\Progress;

class CompleteTimeTest extends \Codeception\Test\Unit
{
    public function testRules() {
        $field = new CompleteTime();
        $this->assertFalse($field->rules);
    }

    public function testScope() {
        $field = new CompleteTime();
        $this->assertFalse($field->scope);
    }

    public function testGetColumn() {
        $model = new CompleteTimeTestModel;
        $currentTime = '2020-03-25 14:00:00';
        $field = new CompleteTime([
            'startTimeAttribute' => 'start_time',
            'totalCountAttribute' => 'total_count',
            'currentCountAttribute' => 'current_count',
            'currentTime' => $currentTime,
        ]);
        $column = $field->getColumn();
        $this->assertArrayHasKey('value', $column);
        $value = $column['value'];
        $this->assertIsCallable($value);
        $this->assertEquals('2020-03-25 18:00:00', $value($model));
    }

    public function testCurrentTimeByDefault() {
        $field = new CompleteTime();
        $this->assertIsString($field->currentTime);
    }

    public function testDisplayOnly() {
        $field = new CompleteTime();
        $this->assertTrue($field->getDisplayOnly());
    }

    public function testGetField() {
        $model = new CompleteTimeTestModel;
        $currentTime = '2020-03-25 14:00:00';
        $field = new CompleteTime([
            'model' => $model,
            'attribute' => 'complete_time',
            'startTimeAttribute' => 'start_time',
            'totalCountAttribute' => 'total_count',
            'currentCountAttribute' => 'current_count',
            'currentTime' => $currentTime,
        ]);
        $formField = $field->getField();
        $this->assertArrayHasKey('value', $formField);
        $value = $formField['value'];
        $this->assertIsCallable($value);
        $this->assertEquals('25.03.2020 18:00:00', $value());
    }

    /**
     * @TODO Needed class extract for value callback
     */
    public function testGetValueWhenEmptyStartTime() {
        $model = new CompleteTimeTestModel;
        $model->start_time = null;
//        $model->total_count = null;
        $currentTime = '2020-03-25 14:00:00';
        $field = new CompleteTime([
            'model' => $model,
            'attribute' => 'complete_time',
            'startTimeAttribute' => 'start_time',
            'totalCountAttribute' => 'total_count',
            'currentCountAttribute' => 'current_count',
            'currentTime' => $currentTime,
        ]);
        $value = $field->getValue();
        $this->assertNull($value);
    }

    /**
     * @TODO Needed class extract for value callback
     */
    public function testGetValueWhenEmptyTotalCount() {
        $model = new CompleteTimeTestModel;
        $model->total_count = null;
        $currentTime = '2020-03-25 14:00:00';
        $field = new CompleteTime([
            'model' => $model,
            'attribute' => 'complete_time',
            'startTimeAttribute' => 'start_time',
            'totalCountAttribute' => 'total_count',
            'currentCountAttribute' => 'current_count',
            'currentTime' => $currentTime,
        ]);
        $value = $field->getValue();
        $this->assertNull($value);
    }

    /**
     * @TODO Needed class extract for value callback
     */
    public function testGetValueWhenEmptyCurrentCount() {
        $model = new CompleteTimeTestModel;
        $model->current_count = null;
        $currentTime = '2020-03-25 14:00:00';
        $field = new CompleteTime([
            'model' => $model,
            'attribute' => 'complete_time',
            'startTimeAttribute' => 'start_time',
            'totalCountAttribute' => 'total_count',
            'currentCountAttribute' => 'current_count',
            'currentTime' => $currentTime,
        ]);
        $value = $field->getValue();
        $this->assertNull($value);
    }
//
//    public function testGetFieldAsWidget() {
//        $model = new CompleteTimeTestModel;
//        $field = new CompleteTime([
//            'model' => $model,
//            'asWidget' => true,
//            'attribute' => 'progress',
//            'totalCountAttribute' => 'total_count',
//            'currentCountAttribute' => 'current_count',
//        ]);
//        $formField = $field->getField();
//        $this->assertArrayHasKey('value', $formField);
//        $value = $formField['value'];
//        $this->assertIsCallable($value);
//        $this->assertEquals(Progress::widget([
//            'id' => 'progressbar-progress',
//            'percent' => '9'
//        ]), $value());
//    }
}

class CompleteTimeTestModel extends \yii\base\Model {
    public $start_time = '2020-03-25 12:00:00';
    public $total_count = 3;
    public $current_count = 1;
}