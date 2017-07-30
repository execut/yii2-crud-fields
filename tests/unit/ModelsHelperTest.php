<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 6/24/17
 * Time: 2:03 PM
 */

namespace execut\crudFields\tests\unit;


use execut\crudFields\fields\Boolean;
use execut\crudFields\fields\Date;
use execut\crudFields\fields\Field;
use execut\crudFields\fields\Id;
use execut\crudFields\ModelsHelper;
use execut\crudFields\ModelsHelperTrait;
use execut\crudFields\TestCase;

class ModelsHelperTest extends TestCase
{
    use ModelsHelperTrait;
    public function testGetStandardFields() {
        $modelsHelper = new ModelsHelper();
        $expectedAttributes = $modelsHelper->standardFieldsDefault;
        $this->assertEquals($expectedAttributes, $this->getStandardFields());
    }

    public function testGetStandardFieldsWithExcludedFields() {
        $modelsHelper = new ModelsHelper();
        $modelsHelper->exclude = ['name'];
        $fields = $modelsHelper->getStandardFields();
        $this->assertArrayNotHasKey('name', $fields);
    }

    public function testGetStandardFieldsWithExcludedFieldsViaTrait() {
        $fields = $this->getStandardFields(['name']);
        $this->assertArrayNotHasKey('name', $fields);
    }

    public function testGetStandardFieldsWithOther() {
        $modelsHelper = new ModelsHelper();
        $modelsHelper->other = [
            'test' => [
                'class' => Boolean::class,
            ],
        ];
        $fields = $modelsHelper->getStandardFields();
        $this->assertArrayHasKey('test', $fields);
    }

    public function testGetStandardFieldsWithOtherViaTrait() {
        $fields = $this->getStandardFields([], [
            'test' => [
                'class' => Boolean::class,
            ],
        ]);
        $this->assertArrayHasKey('test', $fields);
    }
}