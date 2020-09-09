<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\models;

use Codeception\Test\Unit;
use execut\crudFields\models\AllFields\MultipleinputVia;
use execut\crudFields\models\AllFields\Nested;
use execut\crudFields\models\AllFields\Select2Via;
use execut\crudFields\fields\Boolean;
use execut\crudFields\fields\Date;
use execut\crudFields\fields\Field;
use execut\crudFields\fields\HasManyMultipleInput;
use execut\crudFields\fields\HasManySelect2;
use execut\crudFields\fields\HasOneSelect2;
use execut\crudFields\fields\Id;
use execut\crudFields\fields\reloader\Reloader;
use execut\crudFields\fields\reloader\Target;
use execut\crudFields\fields\reloader\type\Periodically;
use execut\crudFields\fields\reloader\type\Dependent;
use execut\crudFields\fields\StringField;
use yii\db\ActiveQuery;

/**
 * AllFieldsTest
 * @package execut\books
 */
class AllFieldsTest extends Unit
{
    public function testGetFieldId()
    {
        $model = new AllFields();
        $id = $model->getField('id');
        $this->assertInstanceOf(Id::class, $id);
    }

    public function testGetNameField()
    {
        $model = new AllFields();
        $field = $model->getField('name');
        $this->assertInstanceOf(StringField::class, $field);
        $this->assertEquals('name', $field->attribute);
        $this->assertTrue($field->required);
    }

    public function testGetFieldBool()
    {
        $model = new AllFields();
        $bool = $model->getField('bool');
        $this->assertInstanceOf(Boolean::class, $bool);
        $this->assertEquals('bool', $bool->attribute);
    }

    public function testHasOneField()
    {
        $model = new AllFields();
        $hasOne = $model->getField('hasOne');
        $this->assertInstanceOf(HasOneSelect2::class, $hasOne);
        $this->assertEquals('has_one_id', $hasOne->attribute);
        $query = $hasOne->getRelationQuery();
        $this->assertInstanceOf(ActiveQuery::class, $query);
        $this->assertEquals([
            '/crudFields/fields'
        ], $hasOne->url);
    }

    public function testPeriodicallyUpdatedField()
    {
        $model = new AllFields();
        $field = $model->getField('periodically_updated');
        $this->assertInstanceOf(Field::class, $field);
        $reloader = current($field->getReloaders());
        $this->assertInstanceOf(Reloader::class, $reloader);
        $this->assertInstanceOf(Periodically::class, $reloader->getType());
    }

    public function testPeriodicallyUpdatedWidgetField()
    {
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

    public function testRecord_for_update_when_a_specific_value_selectedField()
    {
        $model = new AllFields();
        $field = $model->getField('record_for_update_when_a_specific_value_selected');
        $this->assertInstanceOf(Boolean::class, $field);

    }

    public function testChange_updatedField()
    {
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

    public function testspecific_value_selected_updatedField()
    {
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

    public function testEmptyUpdatedField()
    {
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

    public function testNotEmptyUpdatedField()
    {
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

    public function testHasManySelect2Field()
    {
        $model = new AllFields();
        $field = $model->getField('hasManySelect2');
        $this->assertInstanceOf(HasManySelect2::class, $field);
        $this->assertEquals('hasManySelect2', $field->attribute);
        $this->assertEquals([
            '/crudFields/fields'
        ], $field->url);
        $this->assertEquals('hasManySelect2',$field->relationName);
        $relationQuery = $field->relationQuery;
        $this->assertInstanceOf(ActiveQuery::class, $relationQuery);
        $this->assertEquals([
            'has_many_select2_id' => 'id',
        ], $relationQuery->link);
    }

    public function testHasManySelect2ViaField()
    {
        $model = new AllFields();
        $field = $model->getField('hasManySelect2Via');
        $this->assertInstanceOf(HasManySelect2::class, $field);
        $this->assertEquals('hasManySelect2Via', $field->attribute);
        $this->assertEquals([
            '/crudFields/fields'
        ], $field->url);
        $this->assertEquals('hasManySelect2Via',$field->relationName);
        $relationQuery = $field->relationQuery;
        $this->assertInstanceOf(ActiveQuery::class, $relationQuery);
        $this->assertEquals([
            'id' => 'example_all_field_to_id'
        ], $relationQuery->link);
        $via = $relationQuery->via;
        $this->assertArrayHasKey(0, $via);
        $this->assertEquals('select2Via', $via[0]);
    }

    public function testSave()
    {
        $model = new AllFields();
        $model->scenario = Field::SCENARIO_FORM;
        $model->name = 'Selected model';
        $this->assertTrue($model->save());
    }

    public function testSaveHasManySelect2ViaField()
    {
        $model = new AllFields();
        $model->scenario = Field::SCENARIO_FORM;
        $model->name = 'Selected model';
        $this->assertTrue($model->save());
        $id = $model->primaryKey;

        $model = new AllFields();
        $model->scenario = Field::SCENARIO_FORM;
        $select2Via = [
            [
                'id' => $id,
            ]
        ];
        $model->load([
            'name' => 'test',
            'hasManySelect2Via' => $select2Via,
        ], '');
        $this->assertCount(1, $model->hasManySelect2Via);
        $this->assertEquals($id, $model->hasManySelect2Via[0]->id);
        $saveResult = $model->save();
        $this->assertTrue($saveResult, var_export($model->errors, true));
        $count = Select2Via::find()->andWhere([
            'example_all_field_from_id' => $model->primaryKey,
            'example_all_field_to_id' => $id,
        ])->count();
        $this->assertEquals(1, $count);
    }

    public function testGetHasManyMultipleinput()
    {
        $model = new AllFields;
        $query = $model->getRelation('hasManyMultipleinput');
        $this->assertEquals(Nested::class, $query->modelClass);
    }

    public function testGetRecursiveFields() {
        $model = new AllFields;
        $field = $model->getField('hasManyMultipleinput');
        $this->assertIsArray($field->getField());

        $field = $model->getField('hasManyMultipleinputVia');
        $this->assertIsArray($field->getField());
    }

    public function testHasManyMultipleinputField() {
        $model = new AllFields();
        $field = $model->getField('hasManyMultipleinput');
        $this->assertInstanceOf(HasManyMultipleInput::class, $field);
        $this->assertEquals('hasManyMultipleinput', $field->attribute);
        $this->assertEquals('hasManyMultipleinput',$field->relationName);
        $relationQuery = $field->relationQuery;
        $this->assertInstanceOf(ActiveQuery::class, $relationQuery);
        $this->assertEquals([
            'has_many_multipleinput_id' => 'id',
        ], $relationQuery->link);
    }

    public function testGetSelect2Via()
    {
        $model = new AllFields;
        $query = $model->getRelation('select2Via');
        $this->assertEquals(Select2Via::class, $query->modelClass);
    }

    public function testGetMultipleinput()
    {
        $model = new AllFields;
        $query = $model->getRelation('multipleinputVia');
        $this->assertEquals(MultipleinputVia::class, $query->modelClass);
    }

    public function testHasManyMultipleinputViaField() {
        $model = new AllFields();
        $field = $model->getField('hasManyMultipleinputVia');
        $this->assertInstanceOf(HasManyMultipleInput::class, $field);
        $this->assertEquals('hasManyMultipleinputVia', $field->attribute);
        $this->assertEquals('hasManyMultipleinputVia',$field->relationName);
        $relationQuery = $field->relationQuery;
        $this->assertInstanceOf(ActiveQuery::class, $relationQuery);
        $this->assertEquals(Nested::class, $relationQuery->modelClass);
        $this->assertEquals([
            'id' => 'example_all_field_to_id'
        ], $relationQuery->link);
        $via = $relationQuery->via;
        $this->assertArrayHasKey(0, $via);
        $this->assertEquals('multipleinputVia', $via[0]);
    }
}
