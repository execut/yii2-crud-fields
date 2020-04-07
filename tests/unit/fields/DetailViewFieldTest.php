<?php


namespace execut\crudFields\fields;


use execut\crudFields\fields\detailViewField\Addon;
use execut\crudFields\fields\detailViewField\addon\AddonInterface;
use execut\crudFields\TestCase;

class DetailViewFieldTest extends TestCase
{
    public function testGetConfig() {
        $field = new DetailViewField();
        $this->assertEquals([], $field->getConfig());
    }

    public function testGetConfigSimple() {
        $field = new DetailViewField([
            'test' => 'test',
        ]);

        $result = $field->getConfig();
        $this->assertEquals([
            'test' => 'test'
        ], $result);
    }

    public function testGetFieldFalse() {
        $field = new DetailViewField(false);

        $result = $field->getConfig();
        $this->assertFalse($result);
    }

    public function testGetFieldCallable() {
        $fieldTestModel = new FieldTestModel();
        $field = new DetailViewField(function ($factModel, $factField) use ($fieldTestModel) {
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
        ], $field->getConfig($fieldTestModel));
    }

    public function testGetConfigWithAttribute() {
        $field = new DetailViewField([], 'test');

        $this->assertEquals([
            'attribute' => 'test',
        ], $field->getConfig());
    }

    public function testGetConfigWithDisplayOnly() {
        $field = new DetailViewField([
            'displayOnly' => true,
        ]);

        $this->assertEquals([
            'displayOnly' => true,
        ], $field->getConfig());
    }

    public function testGetConfigWithAddon() {
        $addon = $this->getMockBuilder(AddonInterface::class)->onlyMethods(['getConfig'])->getMock();
        $addon->method('getConfig')->willReturn('test');
        $field = new DetailViewField([], null, null, $addon);
        $config = $field->getConfig();
        $this->assertArrayHasKey('fieldConfig', $config);
        $fieldConfig = $config['fieldConfig'];
        $this->assertArrayHasKey('addon', $fieldConfig);
        $this->assertEquals('test', $fieldConfig['addon']);
    }
}