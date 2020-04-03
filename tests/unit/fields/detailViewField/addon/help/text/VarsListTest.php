<?php
/**
 */

namespace execut\crudFields\fields\detailViewField\addon\help\text;


use execut\crudFields\TestCase;

class VarsListTest extends TestCase
{
    public function testGetValue() {
        $text = new VarsList('test', [
            'varName' => 'varDescription',
        ]);
        $this->assertEquals('test<ul><li>varName - varDescription</li></ul>', str_replace("\n", '', $text->getValue()));
    }

    public function testGetInfo() {
        $text = new VarsList('test', [
            'varName' => 'varDescription',
        ]);
        $this->assertEquals('test', $text->getInfo());
    }

    public function testGetVarsList() {
        $text = new VarsList('test', [
            'varName' => 'varDescription',
        ]);
        $this->assertEquals([
            'varName' => 'varDescription',
        ], $text->getVarsList());
    }
}