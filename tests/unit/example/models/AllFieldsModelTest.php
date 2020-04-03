<?php
/**
 */

namespace execut\crudFields\example\models;


use execut\crudFields\fields\Boolean;
use execut\crudFields\fields\Id;
use execut\crudFields\TestCase;

class AllFieldsModelTest extends TestCase
{
    public function testGetFieldId() {
        $model = new AllFieldsModel();
        $id = $model->getField('id');
        $this->assertInstanceOf(Id::class, $id);
    }

    public function testGetFieldBool() {
        $model = new AllFieldsModel();
        $bool = $model->getField('bool');
        $this->assertInstanceOf(Boolean::class, $bool);
        $this->assertEquals('bool', $bool->attribute);
    }
}