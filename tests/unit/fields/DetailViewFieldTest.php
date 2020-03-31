<?php


namespace execut\crudFields\fields;


use execut\crudFields\TestCase;

class DetailViewFieldTest extends TestCase
{
    public function testGetConfig() {
        $field = new DetailViewField();
        $this->assertEquals([], $field->getConfig());
    }



    public function testGetConfigSimple() {
        $field = new DetailViewField(null, [
            'test' => 'test',
        ]);

        $result = $field->getConfig();
        $this->assertEquals([
            'test' => 'test'
        ], $result);
    }

    public function testGetFieldFalse() {
        $field = new DetailViewField(null, false);

        $result = $field->getConfig();
        $this->assertFalse($result);
    }

    public function testGetFieldCallable() {
        $fieldTestModel = new FieldTestModel();
        $field = new DetailViewField($fieldTestModel, function ($factModel, $factField) use ($fieldTestModel) {
                $this->assertEquals($fieldTestModel, $factModel);
                $this->assertInstanceOf(DetailViewField::class, $factField);
                return [
                    'test' => 'test'
                ];
            });

        $this->assertEquals([
            'viewModel' => $fieldTestModel,
            'editModel' => $fieldTestModel,
            'test' => 'test'
        ], $field->getConfig());
    }

    public function testGetConfigWithAttribute() {
        $field = new DetailViewField(null, [], 'test');

        $this->assertEquals([
            'attribute' => 'test',
        ], $field->getConfig());
    }

    public function testGetConfigWithDisplayOnly() {
        $field = new Field([
            'displayOnly' => true,
        ]);

        $this->assertEquals([
            'displayOnly' => true,
        ], $field->getField());
    }
}