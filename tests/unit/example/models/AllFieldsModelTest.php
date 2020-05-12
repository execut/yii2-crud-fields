<?php
/**
 */

namespace execut\crudFields\example\models;


use execut\crudFields\fields\Boolean;
use execut\crudFields\fields\HasOneSelect2;
use execut\crudFields\fields\Id;
use execut\crudFields\TestCase;
use yii\db\ActiveQuery;

class AllFieldsTest extends TestCase
{
    public function testGetFieldId() {
        $model = new AllFields();
        $id = $model->getField('id');
        $this->assertInstanceOf(Id::class, $id);
    }

    public function testGetFieldBool() {
        $model = new AllFields();
        $bool = $model->getField('bool');
        $this->assertInstanceOf(Boolean::class, $bool);
        $this->assertEquals('bool', $bool->attribute);
    }

    public function testHasOneField() {
        $model = new AllFields();
        $hasOne = $model->getField('hasOne');
        $this->assertInstanceOf(HasOneSelect2::class, $hasOne);
        $this->assertEquals('has_one_id', $hasOne->attribute);
        $query = $hasOne->getRelationQuery();
        $this->assertInstanceOf(ActiveQuery::class, $query);
    }
}