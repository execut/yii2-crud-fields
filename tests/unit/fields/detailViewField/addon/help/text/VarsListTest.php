<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields\detailViewField\addon\help\text;

use execut\crudFields\TestCase;

class VarsListTest extends \Codeception\Test\Unit
{
    public function testGetValue()
    {
        $text = new VarsList('test', [
            'varName' => 'varDescription',
        ]);
        $this->assertEquals('test<ul><li>varName - varDescription</li></ul>', str_replace("\n", '', $text->getValue()));
    }

    public function testGetInfo()
    {
        $text = new VarsList('test', [
            'varName' => 'varDescription',
        ]);
        $this->assertEquals('test', $text->getInfo());
    }

    public function testGetVarsList()
    {
        $text = new VarsList('test', [
            'varName' => 'varDescription',
        ]);
        $this->assertEquals([
            'varName' => 'varDescription',
        ], $text->getVarsList());
    }
}
