<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields\reloader\type;

use Codeception\Test\Unit;

class DependentTest extends Unit
{
    public function testGetKey()
    {
        $reloader = new Dependent();
        $this->assertEquals('dependent', $reloader->getKey());
    }
}
