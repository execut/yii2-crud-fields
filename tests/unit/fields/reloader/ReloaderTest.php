<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\tests\unit\fields\reloader;

use Codeception\Test\Unit;
use execut\crudFields\fields\Field;
use execut\crudFields\fields\reloader\Reloader;
use execut\crudFields\fields\reloader\Target;
use execut\crudFields\fields\reloader\TypeInterface;

class ReloaderTest extends Unit
{
    public function testGetKey()
    {
        $type = $this->getMockBuilder(TypeInterface::class)->getMock();
        $type->method('getKey')->willReturn('dependent');
        $reloader = new Reloader($type);
        $this->assertEquals('dependent', $reloader->getKey());
    }

    public function testConstructor()
    {
        $target = $this->getMockBuilder(Target::class)->setConstructorArgs([new Field()])->getMock();
        $type = $this->getMockBuilder(TypeInterface::class)->getMock();
        $reloader = new Reloader($type, [$target]);
        $this->assertEquals([$target], $reloader->getTargets());
    }
}
