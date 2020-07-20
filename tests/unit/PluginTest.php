<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

namespace execut\crudFields;


use Codeception\Test\Unit;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

class PluginTest extends Unit
{
    public function testGetRelationQuery() {
        $owner = new ActiveRecord();
        $params = [
            'test' => 'test',
        ];
        $relationsArray = [
            'test' => $params,
        ];

        $factory = $this->getMockBuilder(QueryFromConfigFactory::class)->getMock();
        $factory->expects($this->once())
            ->method('setModel')
            ->with($owner);
        $factory->expects($this->once())
            ->method('setParams')
            ->with($params);
        $plugin = $this->getMockBuilder(Plugin::class)
            ->onlyMethods(['getRelations'])
            ->setConstructorArgs([
                [
                    'factory' => $factory,
                    'owner' => $owner,
                ],
            ])->getMock();
        $query = new ActiveQuery('test');
        $factory->expects($this->once())
            ->method('create')
            ->willReturn($query);
        $plugin->method('getRelations')
            ->willReturn($relationsArray);
        $this->assertEquals($query, $plugin->getRelationQuery('test'));
        $plugin->getRelationQuery('test');
    }

    public function testGetFactory() {
        $plugin = $this->getMockBuilder(Plugin::class)
            ->onlyMethods([])
            ->getMock();
        $this->assertInstanceOf(QueryFromConfigFactory::class, $plugin->getFactory());
    }
}