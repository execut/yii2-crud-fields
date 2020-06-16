<?php
namespace execut\crudFields\fields\reloader\type;


use Codeception\Test\Unit;

class DependentTest extends Unit
{
    public function testGetKey() {
        $reloader = new Dependent();
        $this->assertEquals('dependent', $reloader->getKey());
    }
}