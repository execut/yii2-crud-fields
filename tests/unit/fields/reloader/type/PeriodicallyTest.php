<?php
namespace execut\crudFields\fields\reloader\type;


use Codeception\Test\Unit;

class PeriodicallyTest extends Unit
{
    public function testGetKey() {
        $reloader = new Periodically();
        $this->assertEquals('periodically', $reloader->getKey());
    }
}