<?php
/**
 */

namespace execut\crudFields\fields;


use execut\crudFields\TestCase;
use kartik\detail\DetailView;
use kartik\grid\GridView;
use yii\db\ActiveQuery;
use yii\web\JsExpression;

class HasOneSelect2Test extends TestCase
{
    public function testGetField() {
        $field = $this->getField();
        $field = $field->getField();
        $this->assertEquals([
            'type' => DetailView::INPUT_SELECT2,
            'attribute' => 'test_test_id',
            'value' => 'test',
            'widgetOptions' => [
                'initValueText' => 'test',
                'pluginOptions' => [
                    'placeholder' => '',
                    'allowClear' => true,
                    'ajax' => [
                        'url' => '/index-test.php?r=url',
                        'dataType' => 'json',
                        'data' => new JsExpression(<<<JS
function(params) {
    return {
        "name": params.term
    };
}
JS
                        )
                    ],
                ],
            ],
        ], $field);
    }

    public function testGetColumn() {
        $field = $this->getField();
        $query = Model::$query = $this->getMockBuilder(ActiveQuery::className())->setConstructorArgs([Model::className()])->setMethods(['andWhere', 'all'])->getMock();
        $query->method('andWhere')->with(['id' => [2]])->willReturn($query);
        $query->method('all')->willReturn([$field->model]);
        $this->assertEquals([
            'attribute' => 'test_test_id',
            'value' => 'testTest.name',
            'filter' => [
                2 => 'test'
            ],
            'filterType' => GridView::FILTER_SELECT2,
            'filterWidgetOptions' => [
                'initValueText' => [
                    2 => 'test'
                ],
                'options' => [
                    'multiple' => true,
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'ajax' => [
                        'url' => '/index-test.php?r=url',
                        'dataType' => 'json',
                        'data' => new JsExpression(<<<JS
function (params) {
  return {
    "name": params.term
  };
}
JS
                        )

                    ],
                ],
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
        $field = new HasOneSelect2([
            'attribute' => 'test_test_id',
            'url' => [
                '/url'
            ],
            'model' => $model,
        ]);

        return $field;
    }
}