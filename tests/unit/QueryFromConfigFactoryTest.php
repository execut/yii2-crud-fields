<?php
/**
 */

namespace execut\crudFields;


use Codeception\Test\Unit;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

class QueryFromConfigFactoryTest extends Unit
{
    public function testRun() {
        $link = [
            'id' => 'relation_id',
        ];
        $inverseOf = 'interseTestRelation';

        $query = new ActiveQuery('a');
        QueryFromConfigFactoryTestModel::$query = $query;

        $callbackIsCalled = false;
        $model = new QueryFromConfigFactoryTestModel();
        $factory = new QueryFromConfigFactory([
            'class' => QueryFromConfigFactoryTestModel::class,
            'viaTable' => 'viaTable',
            'viaLink' => $link,
            'link' => $link,
            'multiple' => true,
            'inverseOf' => $inverseOf,
            'scopes' => [
                function ($q) use ($query, &$callbackIsCalled) {
                    $this->assertEquals($query, $q);
                    $callbackIsCalled = true;
                }
            ],
        ], $model);

        $relation = $factory->create();
        $this->assertEquals($query, $relation);
        $this->assertEquals($model, $relation->primaryModel);
        $this->assertEquals($link, $relation->link);
        $this->assertTrue($relation->multiple);
        $this->assertInstanceOf(ActiveQuery::class, $relation->via);
        $this->assertEquals($inverseOf, $relation->inverseOf);
        $this->assertTrue($callbackIsCalled);
    }

    public function testGetParams() {
        $params = ['test' => 'test'];
        $factory = new QueryFromConfigFactory($params);
        $this->assertEquals($params, $factory->getParams());
    }

    public function testSetParams() {
        $params = ['test' => 'test'];
        $factory = new QueryFromConfigFactory();
        $factory->setParams($params);
        $this->assertEquals($params, $factory->getParams());
    }

    public function testGetModel() {
        $model = new QueryFromConfigFactoryTestModel();
        $factory = new QueryFromConfigFactory([], $model);
        $this->assertEquals($model, $factory->getModel());
    }

    public function testSetModel() {
        $model = new QueryFromConfigFactoryTestModel();
        $factory = new QueryFromConfigFactory();
        $factory->setModel($model);
        $this->assertEquals($model, $factory->getModel());
    }
}

class QueryFromConfigFactoryTestModel extends ActiveRecord {
    public static $query = null;
    public static function find()
    {
        return self::$query;
    }
}