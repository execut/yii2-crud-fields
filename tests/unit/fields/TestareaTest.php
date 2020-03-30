<?php
/**
 */

namespace execut\crudFields\fields;


use execut\crudFields\Relation;
use execut\crudFields\TestCase;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

class TextareaTest extends TestCase
{
    public function testGetColumn() {
        $model = new FieldTestModel();
        $field = new Textarea([
            'attribute' => 'name',
            'model' => $model
        ]);
        $this->assertEquals([
            'attribute' => 'name',
            'label' => 'Name',
        ], $field->getColumn());
    }
}