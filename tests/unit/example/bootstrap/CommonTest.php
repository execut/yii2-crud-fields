<?php


namespace execut\crudFields\example\bootstrap;


use Codeception\Test\Unit;
use execut\crudFields\example\Module;

class CommonTest extends Unit
{
    public function testBootstrap() {
        $bootstrap = new Common;
        $bootstrap->bootstrap(\yii::$app);
        $this->assertInstanceOf(Module::class, \yii::$app->getModule('crudFieldsExample'));
    }
}