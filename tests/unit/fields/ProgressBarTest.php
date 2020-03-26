<?php


namespace execut\crudFields\fields;

use yii\bootstrap\Progress;

class ProgressBarTest extends \Codeception\Test\Unit
{
    public function testGetColumn() {
        $model = new ProgressBarTestModel;
        $field = new ProgressBar([
            'totalCountAttribute' => 'total_count',
            'currentCountAttribute' => 'current_count',
        ]);
        $column = $field->getColumn();
        $this->assertArrayHasKey('value', $column);
        $value = $column['value'];
        $this->assertIsCallable($value);
        $this->assertEquals('9%', $value($model));
    }

    public function testGetColumnWithEmptyCurrentCount() {
        $model = new ProgressBarTestModel;
        $model->total_count = null;
        $field = new ProgressBar([
            'totalCountAttribute' => 'total_count',
            'currentCountAttribute' => 'current_count',
        ]);
        $column = $field->getColumn();
        $this->assertArrayHasKey('value', $column);
        $value = $column['value'];
        $this->assertIsCallable($value);
        $this->assertNull($value($model));
    }

    public function testDisplayOnly() {
        $field = new ProgressBar();
        $this->assertTrue($field->getDisplayOnly());
    }

    public function testGetField() {
        $model = new ProgressBarTestModel;
        $field = new ProgressBar([
            'model' => $model,
            'totalCountAttribute' => 'total_count',
            'currentCountAttribute' => 'current_count',
        ]);
        $formField = $field->getField();
        $this->assertArrayHasKey('value', $formField);
        $value = $formField['value'];
        $this->assertIsCallable($value);
        $this->assertEquals('9%', $value());

        $this->assertArrayHasKey('format', $formField);
        $this->assertEquals('raw', $formField['format']);
    }

    public function testGetFieldWithEmptyValue() {
        $model = new ProgressBarTestModel;
        $model->total_count = null;
        $field = new ProgressBar([
            'model' => $model,
            'totalCountAttribute' => 'total_count',
            'currentCountAttribute' => 'current_count',
        ]);
        $formField = $field->getField();
        $this->assertArrayHasKey('value', $formField);
        $value = $formField['value'];
        $this->assertIsCallable($value);
        $this->assertEquals('-', $value());
    }

    public function testGetFieldAsWidget() {
        $model = new ProgressBarTestModel;
        $field = new ProgressBar([
            'model' => $model,
            'asWidget' => true,
            'attribute' => 'progress',
            'totalCountAttribute' => 'total_count',
            'currentCountAttribute' => 'current_count',
        ]);
        $formField = $field->getField();
        $this->assertArrayHasKey('value', $formField);
        $value = $formField['value'];
        $this->assertIsCallable($value);
        $this->assertEquals(Progress::widget([
            'id' => 'progressbar-progress',
            'percent' => '9',
            'label' => '9%'
        ]), $value());
    }
}

class ProgressBarTestModel extends \yii\base\Model {
    public $total_count = 33;
    public $current_count = 3;
}