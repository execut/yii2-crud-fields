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
        $this->assertEquals([
            'attribute' => 'name',
            'filter' => DateRangePicker::widget([
                'attribute' => 'name',
                'model' => $model,
                'convertFormat'=>true,
                'pluginOptions'=>[
                    'timePicker'=>true,
                    'timePickerIncrement'=>15,
                    'locale'=>['format'=>'Y-m-d']
                ]
            ]),
        ], $field->getColumn());
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