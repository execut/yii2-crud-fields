<?php
/**
 */

namespace execut\crudFields\fields\detailViewField\addon;


use execut\crudFields\TestCase;

class AddonArrayTest extends TestCase
{
    public function testGetConfig() {
        $addon = new AddonArray([
            'key' => 'option',
        ], 'test content');
        $this->assertEquals([
            'append' => [
                'options' => [
                    'key' => 'option',
                ],
                'content' => 'test content',
            ],
        ], $addon->getConfig());
    }
}