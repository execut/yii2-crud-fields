<?php
/**
 */

namespace execut\crudFields\example\models;


use execut\crudFields\fields\Boolean;
use execut\crudFields\fields\Date;
use execut\crudFields\fields\Field;
use execut\crudFields\fields\HasOneSelect2;
use execut\crudFields\fields\Id;
use execut\crudFields\fields\reloader\Reloader;
use execut\crudFields\fields\reloader\Target;
use execut\crudFields\fields\reloader\type\Periodically;
use execut\crudFields\fields\reloader\type\Dependent;
use execut\crudFields\fields\StringField;
use execut\crudFields\TestCase;
use yii\db\ActiveQuery;

class AllFieldsTest extends TestCase
{
    public function testGetFieldId() {
        $model = new AllFields();
        $id = $model->getField('id');
        $this->assertInstanceOf(Id::class, $id);
    }

    public function testGetNameField() {
        $model = new AllFields();
        $field = $model->getField('name');
        $this->assertInstanceOf(StringField::class, $field);
        $this->assertEquals('name', $field->attribute);
        $this->assertTrue($field->required);
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
        $this->assertEquals([
            '/crudFieldsExample/all-fields'
        ], $hasOne->url);
    }

    public function testPeriodicallyUpdatedField() {
        $model = new AllFields();
        $field = $model->getField('periodically_updated');
        $this->assertInstanceOf(Field::class, $field);
        $reloader = current($field->getReloaders());
        $this->assertInstanceOf(Reloader::class, $reloader);
        $this->assertInstanceOf(Periodically::class, $reloader->getType());
    }

    public function testPeriodicallyUpdatedWidgetField() {
        $model = new AllFields();
        $field = $model->getField('periodicallyUpdatedWidget');
        $this->assertInstanceOf(HasOneSelect2::class, $field);
        $this->assertEquals('periodically_updated_widget_id', $field->attribute);
        $query = $field->getRelationQuery();
        $this->assertInstanceOf(ActiveQuery::class, $query);
        $reloader = current($field->getReloaders());
        $this->assertInstanceOf(Reloader::class, $reloader);
        $this->assertInstanceOf(Periodically::class, $reloader->getType());
    }

    public function testRecord_for_update_when_a_specific_value_selectedField() {
        $model = new AllFields();
        $field = $model->getField('record_for_update_when_a_specific_value_selected');
        $this->assertInstanceOf(Boolean::class, $field);

    }

    public function testChange_updatedField() {
        $model = new AllFields();
        $field = $model->getField('change_updated');
        $this->assertInstanceOf(Date::class, $field);
        $this->assertTrue($field->isTime);

        $reloader = current($field->getReloaders());
        $this->assertInstanceOf(Reloader::class, $reloader);
        $targets = $reloader->getTargets();
        $this->assertCount(1, $targets);
        $target = current($targets);
        $targetField = $target->getField();
        $this->assertEquals( $model->getField('hasOne'), $targetField);
        $this->assertInstanceOf(Dependent::class, $reloader->getType());
    }

    public function testspecific_value_selected_updatedField() {
        $model = $this->getMockBuilder(AllFields::class)->onlyMethods(['findRecordForUpdateWhenSpecificValueSelected'])->getMock();
        $model->method('findRecordForUpdateWhenSpecificValueSelected')
            ->willReturn(1);
        $field = $model->getField('specific_value_selected_updated');
        $this->assertInstanceOf(Date::class, $field);
        $this->assertTrue($field->isTime);

        $reloader = current($field->getReloaders());
        $this->assertInstanceOf(Reloader::class, $reloader);
        $targets = $reloader->getTargets();
        $this->assertCount(1, $targets);
        $target = current($targets);
        $targetField = $target->getField();
        $this->assertEquals( $model->getField('hasOne'), $targetField);
        $this->assertEquals([1], $target->getValues());
        $this->assertInstanceOf(Dependent::class, $reloader->getType());
    }

    public function testEmpty_updatedField() {
        $model = new AllFields();
        $field = $model->getField('empty_updated');
        $this->assertInstanceOf(Date::class, $field);
        $this->assertTrue($field->isTime);

        $reloader = current($field->getReloaders());
        $this->assertInstanceOf(Reloader::class, $reloader);
        $targets = $reloader->getTargets();
        $this->assertCount(1, $targets);
        $target = current($targets);
        $targetField = $target->getField();
        $this->assertEquals( $model->getField('hasOne'), $targetField);
        $this->assertTrue($target->getWhenIsEmpty());
        $this->assertInstanceOf(Dependent::class, $reloader->getType());
    }

    public function testNot_empty_updatedField() {
        $model = new AllFields();
        $field = $model->getField('not_empty_updated');
        $this->assertInstanceOf(Date::class, $field);
        $this->assertTrue($field->isTime);

        $reloader = current($field->getReloaders());
        $this->assertInstanceOf(Reloader::class, $reloader);
        $this->assertInstanceOf(Dependent::class, $reloader->getType());
        $targets = $reloader->getTargets();
        $this->assertCount(1, $targets);
        $target = current($targets);
        $targetField = $target->getField();
        $this->assertEquals( $model->getField('hasOne'), $targetField);
        $this->assertFalse($target->getWhenIsEmpty());
    }
}