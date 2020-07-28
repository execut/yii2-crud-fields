<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields\detailViewField\addon;

use execut\crudFields\TestCase;

class AddonArrayTest extends \Codeception\Test\Unit
{
    public function testGetConfig()
    {
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
