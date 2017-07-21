<?php
/**
 */

namespace execut\crudFields\fields;


use execut\crudFields\TestCase;
use kartik\daterange\DateRangePicker;
use kartik\detail\DetailView;
use kartik\grid\GridView;

class DateTest extends TestCase
{
    public function testGetColumn() {
        $model = new Model();
        $field = new Date([
            'attribute' => 'name',
            'model' => $model
        ]);
        $column = $field->getColumn();
        $this->assertArrayHasKey('filter', $column);
        $column['filter'] = preg_replace('/daterangepicker_[a-z\d]+/', '', $column['filter']);
        $this->assertEquals([
            'attribute' => 'name',
            'filter' => preg_replace('/daterangepicker_[a-z\d]+/', '', DateRangePicker::widget([
                'attribute' => 'name',
                'model' => $model,
                'convertFormat'=>true,
                'pluginOptions'=>[
                    'locale'=>['format'=>'Y-m-d']
                ]
            ])),
        ], $column);
    }

    public function testGetColumnWithTime() {
        $model = new Model();
        $field = new Date([
            'attribute' => 'name',
            'model' => $model,
            'isTime' => true,
        ]);
        $column = $field->getColumn();
        $this->assertArrayHasKey('filter', $column);
        $column['filter'] = preg_replace('/daterangepicker_[a-z\d]+/', '', $column['filter']);
        $this->assertEquals([
            'attribute' => 'name',
            'filter' => preg_replace('/daterangepicker_[a-z\d]+/', '', DateRangePicker::widget([
                'attribute' => 'name',
                'model' => $model,
                'convertFormat'=>true,
                'pluginOptions'=>[
                    'timePicker'=>true,
                    'timePickerIncrement'=>15,
                    'locale'=>['format'=>'Y-m-d H:i:s']
                ]
            ])),
        ], $column);
    }

    public function testGetField() {
        $field = new Date([
            'attribute' => 'name',
        ]);
        $this->assertEquals([
            'attribute' => 'name',
            'displayOnly' => true,
        ], $field->getField());
    }
}