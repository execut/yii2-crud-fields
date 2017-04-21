<?php
/**
 */

namespace execut\crudFields\fields;


use execut\crudFields\TestCase;
use kartik\detail\DetailView;
use kartik\grid\GridView;
use yii\db\ActiveQuery;
use yii\web\JsExpression;

class HasOneRelationTest extends TestCase
{
    public function testGetRelationByAttribute() {
        $field = $this->getField();
        $this->assertEquals('testTest', $field->relation);
    }

    /**
     * @return array|HasOneRelation
     */
    protected function getField()
    {
        $model = new Model();
        $model->testTest = $model;
        $field = new HasOneRelation([
            'attribute' => 'test_test_id',
            'model' => $model,
        ]);

        return $field;
    }
}