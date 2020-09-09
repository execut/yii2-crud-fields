<?php

/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

namespace execut\crudFields\fields\HasManyMultipleInput\GridRenderer\Params;

use Codeception\Test\Unit;
use execut\crudFields\models\AllFields;
use yii\data\ActiveDataProvider;

class FieldTest extends Unit
{
    public function testToArray()
    {
        $model = new AllFields();
        $params = new Field('hasManyMultipleinput', [
            'test' => 'test',
        ], $model);
        $result = $params->toArray();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('columns', $result);
        $this->assertEquals([
            'test' => 'test',
        ], $result['columns']);
        unset($result['columns']);

        $this->assertArrayHasKey('dataProvider', $result);
        $this->assertInstanceOf(ActiveDataProvider::class, $result['dataProvider']);
        unset($result['dataProvider']);

        $this->assertEquals([
            'layout' => '{toolbar}{summary}{items}{pager}',
            'bordered' => false,
            'showOnEmpty' => true,
        ], $result);
    }
}
