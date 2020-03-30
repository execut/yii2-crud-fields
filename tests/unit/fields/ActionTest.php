<?php
/**
 */

namespace execut\crudFields\fields;


use execut\crudFields\TestCase;
use kartik\grid\ActionColumn;
use yii\db\ActiveQuery;

class ActionTest extends TestCase
{
    public function testGetField() {
        $field = new Action();
        $this->assertFalse($field->getField());
    }

    public function testGetColumn() {
        $field = new Action([
            'update' => true,
            'delete' => false,
            'view' => false,
        ]);
        $column = $field->getColumn();
        $this->assertEquals([
            'class' => ActionColumn::class,
            'template' => '{update}',
            'deleteOptions' => [
                'class' => 'btn btn-danger glyphicon glyphicon-remove',
                'label' => '',
            ],
            'options' => [
                'style' => [
                    'min-width' => '156px',
                ],
            ],
        ], $column);
    }

    public function testGetEmptyColumn() {
        $field = new Action([
            'update' => false,
            'delete' => false,
            'view' => false,
        ]);
        $column = $field->getColumn();
        $this->assertFalse($column);
    }

    public function testApplyScopes() {
        $field = new Action();
        $q = new ActiveQuery([
            'modelClass' => FieldTestModel::class,
        ]);
        $this->assertEquals($q, $field->applyScopes($q));
    }

    public function testRules() {
        $field = new Action();
        $this->assertFalse($field->rules());
    }
}