<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields\detailViewField\addon\help\text;

use execut\crudFields\TestCase;

class SimpleTest extends \Codeception\Test\Unit
{
    public function testGetValue()
    {
        $text = new Simple('test');
        $this->assertEquals('test', $text->getValue());
    }
}
