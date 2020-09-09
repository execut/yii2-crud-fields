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
use execut\crudFields\tests\unit\Model;
use yii\data\ArrayDataProvider;
use yii\db\ActiveQueryInterface;

class ColumnTest extends Unit
{
    public function testToArrayWithEmptyRelation()
    {
        $model = new AllFields();
        $params = new Column('hasManyMultipleinput', [
            'test' => 'test',
        ], $model);
        $result = $params->toArray();
        $this->assertNull($result);
    }

    public function testToArrayWithPopulatedRelation()
    {
        $model = new AllFields();
        $model->populateRelation('hasManyMultipleinput', [
            new AllFields()
        ]);
        $params = new Column('hasManyMultipleinput', [
            'test' => 'test',
        ], $model);
        $result = $params->toArray();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('dataProvider', $result);
        $dataProvider = $result['dataProvider'];
        $this->assertInstanceOf(ArrayDataProvider::class, $dataProvider);
        $this->assertCount(1, $dataProvider->allModels);
    }

    public function testToArrayWithNotPopulatedRelation()
    {
        $model = new Model();
        $query = $this->getMockBuilder(ActiveQueryInterface::class)->getMock();
        $query->expects($this->once())
            ->method('limit')
            ->with(10)
            ->willReturn($query);
        $query->method('all')
            ->willReturn([$model]);
        $model->setRelation('testRelation', $query);
        $params = new Column('testRelation', [
            'test' => 'test',
        ], $model, 10);
        $result = $params->toArray();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('dataProvider', $result);
        $dataProvider = $result['dataProvider'];
        unset($result['dataProvider']);
        $this->assertInstanceOf(ArrayDataProvider::class, $dataProvider);
        $this->assertCount(1, $dataProvider->allModels);
        unset($result['columns']);
        $this->assertEquals([
            'layout' => '{items}',
            'export' => false,
            'resizableColumns' => false,
            'bordered' => false,
            'toolbar' => '',
            'showOnEmpty' => true,
        ], $result);
    }

    public function testToArrayWithNotPopulatedRelationWithoutLimit()
    {
        $model = new Model();
        $query = $this->getMockBuilder(ActiveQueryInterface::class)->getMock();
        $query->expects($this->never())
            ->method('limit');
        $query->method('all')
            ->willReturn([]);
        $model->setRelation('testRelation', $query);
        $params = new Column('testRelation', [
            'test' => 'test',
        ], $model, false);
        $result = $params->toArray();
    }
}
