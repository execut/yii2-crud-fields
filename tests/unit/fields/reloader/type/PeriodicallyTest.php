<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\tests\unit\fields\reloader\type;

use Codeception\Test\Unit;
use execut\crudFields\fields\reloader\type\Periodically;

class PeriodicallyTest extends Unit
{
    public function testGetKey()
    {
        $reloader = new Periodically();
        $this->assertEquals('periodically', $reloader->getKey());
    }
}
