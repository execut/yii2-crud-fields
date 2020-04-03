<?php
/**
 */

namespace execut\crudFields\fields\detailViewField\addon\help\text;
use execut\crudFields\TestCase;

class SimpleTest extends TestCase
{
    public function testGetValue() {
        $text = new Simple('test');
        $this->assertEquals('test', $text->getValue());
    }
}