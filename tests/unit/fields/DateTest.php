<?php
/**
 */

namespace execut\crudFields\fields;


use execut\crudFields\TestCase;
use kartik\daterange\DateRangePicker;
use kartik\detail\DetailView;
use kartik\grid\GridView;
use yii\web\View;

class DateTest extends TestCase
{
    protected $oldView = null;
    public function setUp():void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $view = $this->getMockBuilder(View::class)->setMethods(['registerAssetBundle'])->getMock();
        $view->method('registerAssetBundle')->willReturn(true);
        $this->mockWebApplication([
            'components' => [
                'view' => $view,
            ],
        ]);
    }

    public function tearDown():void
    {
        $this->destroyApplication();
        parent::tearDown();
    }

    public function testGetColumn() {
        $model = new Model();
        $field = new Date([
            'attribute' => 'name',
            'model' => $model,
        ]);
        $column = $field->getColumn();
        $this->assertArrayHasKey('filter', $column);
        $column['filter'] = preg_replace('/daterangepicker_[a-z\d]+/', '', $column['filter']);
        $this->assertEquals([
            'attribute' => 'name',
            'label' => 'Name',
            'filter' => preg_replace('/daterangepicker_[a-z\d]+/', '', DateRangePicker::widget([
                'attribute' => 'name',
                'model' => $model,
                'convertFormat' =>true,
                'pluginOptions' => [
                    'locale' => ['format' =>'Y-m-d']
                ]
            ])),
            'format' => function () {},
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
                'convertFormat' =>true,
                'pluginOptions' => [
                    'timePicker' =>true,
                    'timePickerIncrement' =>15,
                    'locale' => ['format' =>'Y-m-d H:i:s']
                ]
            ])),
            'format' => function () {},
            'label' => 'Name',
        ], $column);
    }

    public function testGetField() {
        $model = new Model();
        $field = new Date([
            'attribute' => 'name',
            'model' => $model,
        ]);
        $this->assertEquals([
            'attribute' => 'name',
            'displayOnly' => true,
            'viewModel' => $model,
            'editModel' => $model,
            'value' => function () {},
        ], $field->getField());
    }
}