<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\tests\unit;

use execut\crudFields\Behavior;
use execut\crudFields\fields\Action;
use execut\crudFields\fields\Field;
use execut\crudFields\Plugin;
use PHPUnit\Framework\TestCase;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

class BehaviorTest extends \Codeception\Test\Unit
{
    public function testGetModuleWithoutOwner()
    {
        $behavior = new Behavior();
        $this->assertNull($behavior->getModule());
    }

    public function testGetModuleDetectModule()
    {
        $behavior = new Behavior([
            'owner' => new Model()
        ]);
        $this->assertEquals('crudFields', $behavior->getModule());
    }

    public function testSetFields()
    {
        $model = new Model;
        $behavior = new Behavior([
            'owner' => $model,
            'fields' => [
                [
                    'attribute' => 'name',
                ],
            ],
        ]);
        $fields = $behavior->fields;
        $this->assertArrayHasKey(0, $fields);
        $field = $fields[0];
        $this->assertInstanceOf(Field::class, $field);
        $this->assertEquals($model, $field->model);
    }

    public function testGetRelation()
    {
        $field = $this->getMockBuilder(Field::class)->getMock();
        $q = new ActiveQuery('a');
        $field->method('getRelationQuery')->willReturn($q);

        $relationName = 'testRelationName';
        $field->method('getRelationName')->willReturn($relationName);

        $behavior = new Behavior([
            'fields' => [
                $relationName => $field
            ]
        ]);
        $relation = $behavior->getRelation($relationName);
        $this->assertEquals($q, $relation);
    }

    public function testGetRelationFromPlugin()
    {
        $plugin = $this->getMockBuilder(BehaviorTestPlugin::class)->onlyMethods(['getRelationQuery'])->getMock();
        $query = new ActiveQuery('a');
        Model::$query = $query;

        $plugin->method('getRelationQuery')
            ->with('testRelation')
            ->willReturn($query);
        $model = new Model();

        $behavior = new Behavior([
            'owner' => $model,
            'plugins' => [
                'testPlugin' => $plugin,
            ]
        ]);

        $relation = $behavior->getRelation('testRelation');
        $this->assertEquals($query, $relation);
    }

    public function testSetFieldsByAttribute()
    {
        $model = new Model;
        $behavior = new Behavior([
            'owner' => $model,
            'fields' => [
                'name',
            ],
        ]);
        $fields = $behavior->fields;
        $this->assertCount(1, $fields);
    }

    public function testSetFieldsByClass()
    {
        $model = new Model;
        $behavior = new Behavior([
            'owner' => $model,
            'fields' => [
                Field::class,
            ],
        ]);
        $fields = $behavior->fields;
        $this->assertCount(1, $fields);
        $this->assertNull($fields[0]->attribute);
    }

    public function testGetGridColumns()
    {
        $field = $this->getMockBuilder(Field::class)->setMethods(['getColumn'])->getMock();
        $fieldConfig = [
            'test' => 'test',
        ];
        $field->expects($this->once())->method('getColumn')->willReturn($fieldConfig);
        $behavior = new Behavior([
            'fields' => [
                $field
            ],
        ]);
        $this->assertEquals([
            [
                'test' => 'test'
            ]
        ], $behavior->getGridColumns());
    }

    public function testGetFormFields()
    {
        $field = $this->getMockBuilder(Field::class)->getMock();
        $fieldConfig = [
            'test' => [
                'test' => 'test',
            ]
        ];
        $field->expects($this->once())->method('getFields')->willReturn($fieldConfig);
        $behavior = new Behavior([
            'fields' => [
                $field
            ],
        ]);
        $formFields = $behavior->getFormFields();
        $this->assertEquals([
            'test' => [
                'test' => 'test',
            ]
        ], $formFields);
    }

    public function testGetQueryDefault()
    {
        $model = new Model();
        $behavior = new Behavior([
            'owner' => $model
        ]);

        $q = new ActiveQuery(Model::class);
        $model::$query = $q;
        $this->assertEquals($q, $behavior->getQuery());
    }

    public function testSetQueryFromConstructor()
    {
        $q = new ActiveQuery(Model::class);
        $behavior = new Behavior([
            'query' => $q,
        ]);
        $this->assertEquals($q, $behavior->getQuery());
    }

    public function testSetQuery()
    {
        $behavior = new Behavior();
        $q = new ActiveQuery(Model::class);
        $this->assertEquals($behavior, $behavior->setQuery($q));
        $this->assertEquals($q, $behavior->getQuery());
    }

    public function testApplyScopes()
    {
        $field = $this->getMockBuilder(Field::class)->getMock();
        $model = new Model;
        $q = $this->getMockBuilder(ActiveQuery::class)->setConstructorArgs([
            'modelClass' => Model::class,
        ]) ->getMock();

        $field->expects($this->once())->method('applyScopes')->with($q)->willReturn($q);
        $behavior = new Behavior([
            'owner' => $model,
            'fields' => [
                $field
            ],
        ]);
        $result = $behavior->applyScopes($q);
        $this->assertEquals($q, $result);
    }

    public function testApplyScopesFromConfig()
    {
        $q = new ActiveQuery([
            'modelClass' => Model::class,
        ]);
        $isScopeApplied = false;
        $behavior = new Behavior([
            'scopes' => [
                function () use (&$isScopeApplied) {
                    $isScopeApplied = true;
                }
            ],
        ]);
        $behavior->applyScopes($q);
        $this->assertTrue($isScopeApplied);
    }

    public function testSearch()
    {
        $q = new ActiveQuery([
            'modelClass' => Model::class,
        ]);

        $behavior = $this->getMockBuilder(Behavior::class)->onlyMethods(['applyScopes'])->setConstructorArgs([['query' => $q]])->getMock();

        $behavior->method('applyScopes')->with($q)->willReturn($q);
        $result = $behavior->search();
        $this->assertInstanceOf(ActiveDataProvider::class, $result);
        $this->assertEquals($q, $result->query);
    }

    public function testRules()
    {
        $field = $this->getMockBuilder(Field::class)->setMethods(['rules'])->getMock();
        $fieldConfig = [
            [
                'test' => 'test',
            ],
        ];
        $field->expects($this->once())->method('rules')->willReturn($fieldConfig);
        $behavior = new Behavior([
            'fields' => [
                $field
            ],
        ]);
        $this->assertEquals([
            [
                'test' => 'test'
            ]
        ], $behavior->rules());
    }

    public function testReturnFalse()
    {
        $field = $this->getMockBuilder(Field::class)->onlyMethods(['rules', 'getField', 'getColumn'])->getMock();
        $field->expects($this->once())->method('rules')->willReturn(false);
        $field->expects($this->once())->method('getField')->willReturn(false);
        $field->expects($this->once())->method('getColumn')->willReturn(false);
        $behavior = new Behavior([
            'fields' => [
                $field
            ],
        ]);
        $this->assertEquals([], $behavior->rules());
        $this->assertEquals([], $behavior->getFormFields());
        $this->assertEquals([], $behavior->getGridColumns());
    }

    public function testGetNotExistedField()
    {
        $behavior = new Behavior();
        $this->assertNull($behavior->getField('test'));
    }

    public function testGetScopes()
    {
        $behavior = new Behavior([
            'scopes' => [
                function ($q) {
                }
            ]
        ]);
        $this->assertCount(1, $behavior->getScopes());
    }

    public function testGetPlugins()
    {
        $behavior = new Behavior([
            'plugins' => [
                'test' => [
                    'class' => BehaviorTestPlugin::class,
                ]
            ]
        ]);
        $this->assertCount(1, $behavior->getPlugins());
    }

    public function testGetPluginByKey()
    {
        $behavior = new Behavior([
            'plugins' => [
                'test' => [
                    'class' => BehaviorTestPlugin::class,
                ]
            ]
        ]);
        $this->assertInstanceOf(BehaviorTestPlugin::class, $behavior->getPlugin('test'));
    }

    public function testGetPluginByNull()
    {
        $behavior = new Behavior();
        $this->assertNull($behavior->getPlugin('test'));
    }

    public function testInitPluginFromString()
    {
        $behavior = new Behavior([
            'plugins' => [
                'test' => BehaviorTestPlugin::class,
            ]
        ]);
        $this->assertInstanceOf(BehaviorTestPlugin::class, $behavior->getPlugin('test'));
    }

    public function testTriggerPluginsEvents()
    {
        $delegatedEvents = [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
            ActiveRecord::EVENT_BEFORE_DELETE => 'beforeDelete',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeInsert',
        ];

        $plugin = $this->getMockBuilder(BehaviorTestPlugin::class)->onlyMethods(array_values($delegatedEvents))->getMock();
        $behavior = new Behavior([
            'owner' => new Model,
        ]);
        $events = $behavior->events();

        foreach ($delegatedEvents as $event => $method) {
            $this->assertArrayHasKey($event, $events);
            $this->assertEquals($method, $events[$event]);
            $plugin->expects($this->once())->method($method);
        }
        $behavior->setPlugins([
            'test' => $plugin
        ]);

        foreach ($delegatedEvents as $event => $method) {
            $behavior->$method();
        }
    }
}

class Model extends ActiveRecord
{
    public $isNewRecord = true;
    public static $query = null;
    public $relation = [];
    public $testAttribute = 'test value';
    public static function find()
    {
        return self::$query;
    }

    protected $relations = [];
    public function setRelation($name, $query)
    {
        $this->relations[$name] = $query;
    }

    public function getRelation($name, $throwException = true)
    {
        if (!empty($this->relations[$name])) {
            return $this->relations[$name];
        }

        return parent::getRelation($name, $throwException); // TODO: Change the autogenerated stub
    }

    public function getGridColumns()
    {
        return [
            'actions' => [
                'class' => Action::class,
            ],
            'testAttribute' => [
                'attribute' => 'testAttribute',
            ]
        ];
    }

    public $attributes = [];
}

class BehaviorTestPlugin extends Plugin
{
}
