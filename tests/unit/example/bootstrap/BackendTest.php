<?php


namespace execut\crudFields\example\bootstrap;


use Codeception\Test\Unit;
use execut\crudFields\example\bootstrap\backend\Bootstrapper;

class BackendTest extends Unit
{
    public function testBootstrap() {
        $bootstrap = new Backend();

        $this->assertInstanceOf(Bootstrapper::class, $bootstrap->getBootstrapper());
    }
}