<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\tests\unit\fields;

use execut\crudFields\fields\DetailViewField;
use execut\crudFields\fields\detailViewField\addon\AddonInterface;
use execut\crudFields\TestCase;

class DetailViewFieldTest extends \Codeception\Test\Unit
{
    public function testGetConfig()
    {
        $field = new DetailViewField();
        $this->assertEquals([], $field->getConfig());
    }

    public function testGetConfigSimple()
    {
        $field = new DetailViewField([
            'test' => 'test',
        ]);

        $result = $field->getConfig();
        $this->assertEquals([
            'test' => 'test'
        ], $result);
    }

    public function testGetFieldFalse()
    {
        $field = new DetailViewField(false);

        $result = $field->getConfig();
        $this->assertFalse($result);
    }

    public function testGetFieldCallable()
    {
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

    public function testGetConfigWithAttribute()
    {
        $field = new DetailViewField([], 'test');

        $this->assertEquals([
            'attribute' => 'test',
        ], $field->getConfig());
    }

    public function testGetConfigWithDisplayOnly()
    {
        $field = new DetailViewField([
            'displayOnly' => true,
        ]);

        $this->assertEquals([
            'displayOnly' => true,
        ], $field->getConfig());
    }

    public function testGetDisplayOnlyByDefault()
    {
        $field = new DetailViewField();
        $this->assertFalse($field->getDisplayOnly());
    }

    public function testGetDisplayOnlyFromCallback()
    {
        $field = new DetailViewField([], null, function () {
            return false;
        });
        $this->assertFalse($field->getDisplayOnly());
    }

    public function testGetConfigWithAddon()
    {
        $addon = $this->getMockBuilder(AddonInterface::class)->onlyMethods(['getConfig'])->getMock();
        $addon->method('getConfig')->willReturn('test');
        $field = new DetailViewField([], null, null, $addon);
        $config = $field->getConfig();
        $this->assertArrayHasKey('fieldConfig', $config);
        $fieldConfig = $config['fieldConfig'];
        $this->assertArrayHasKey('addon', $fieldConfig);
        $this->assertEquals('test', $fieldConfig['addon']);
    }

    public function testHide()
    {
        $field = new DetailViewField([]);
        $this->assertEquals($field, $field->hide());
        $this->assertEquals([
            'rowOptions' => [
                'class' => 'hide',
            ]
        ], $field->getConfig());
    }

    public function testShow()
    {
        $field = new DetailViewField([]);
        $field->hide()->show();
        $this->assertEquals([], $field->getConfig());
    }
}
