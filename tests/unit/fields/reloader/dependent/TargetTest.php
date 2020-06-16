<?php

namespace execut\crudFields\fields\reloader;


use execut\crudFields\fields\Field;

class TargetTest extends \PHPUnit_Framework_TestCase
{
    public function testSetFieldFromConstructor()
    {
        $field = new Field();
        $target = new Target($field);
        $this->assertEquals($field, $target->getField());
    }

    public function testGetValuesWithCallback() {
        $field = new Field();
        $target = new Target($field);
        $target->setValues([function () {
            return 1;
        }]);
        $this->assertEquals([1], $target->getValues());
    }
}
