<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
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
        $model = new FieldTestModel();
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
            'filter' => 1,
            'format' => function () {},
        ], $column);
    }

    public function testGetColumnWithTime() {
        $model = new FieldTestModel();
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
            'filter' => 1,
            'format' => function () {},
            'label' => 'Name',
        ], $column);
    }

    public function testGetField() {
        $model = new FieldTestModel();
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