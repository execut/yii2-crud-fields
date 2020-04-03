<?php
/**
 */

namespace execut\crudFields;


use execut\crudFields\Behavior;
use execut\crudFields\fields\Field;
use PHPUnit\Framework\TestCase;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

class BehaviorTest extends TestCase
{
    public function testGetModuleWithoutOwner() {
        $behavior = new Behavior();
        $this->assertNull($behavior->getModule());
    }

    public function testGetModuleDetectModule() {
        $behavior = new Behavior([
            'owner' => new Model()
        ]);
        $this->assertEquals('crudFields', $behavior->getModule());
    }

    public function testSetFields() {
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

    public function testSetFieldsByAttribute() {
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

    public function testSetFieldsByClass() {
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

    public function testGetGridColumns() {
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

    public function testGetFormFields() {
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
            '0_test' => [
                'test' => 'test',
            ]
        ], $formFields);
    }

    public function testGetQueryDefault() {
        $model = new Model();
        $behavior = new Behavior([
            'owner' => $model
        ]);

        $q = new ActiveQuery([
            'modelClass' => Model::class,
        ]);
        $model::$query = $q;
        $this->assertEquals($q, $behavior->getQuery());
    }

    public function testSetQueryFromConstructor() {
        $q = new ActiveQuery([
            'modelClass' => Model::class,
        ]);
        $behavior = new Behavior([
            'query' => $q,
        ]);
        $this->assertEquals($q, $behavior->getQuery());
    }

    public function testSetQuery() {
        $behavior = new Behavior();
        $q = new ActiveQuery([
            'modelClass' => Model::class,
        ]);
        $this->assertEquals($behavior, $behavior->setQuery($q));
        $this->assertEquals($q, $behavior->getQuery());
    }

    public function testApplyScopes() {
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

    public function testApplyScopesFromConfig() {
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

    public function testSearch() {
        $q = new ActiveQuery([
            'modelClass' => Model::class,
        ]);

        $behavior = $this->getMockBuilder(Behavior::class)->onlyMethods(['applyScopes'])->setConstructorArgs([['query' => $q]])->getMock();

        $behavior->method('applyScopes')->with($q)->willReturn($q);
        $result = $behavior->search();
        $this->assertInstanceOf(ActiveDataProvider::class, $result);
        $this->assertEquals($q, $result->query);
    }

    public function testRules() {
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

    public function testReturnFalse() {
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

    public function testGetNotExistedField() {
        $behavior = new Behavior();
        $this->assertNull($behavior->getField('test'));
    }

    public function testGetFieldsViaRoles() {
        $behavior = new Behavior([
            'roles' => [
                'testRole' => [
                    'fields' => [
                        'test2' => Field::class,
                    ]
                ],
            ]
        ]);
        $this->assertCount(0, $behavior->getFields());
        $behavior->setRole('testRole');
        $this->assertCount(1, $behavior->getFields());
    }

    public function testGetScopes() {
        $behavior = new Behavior([
            'role' => 'testRole',
            'roles' => [
                'testRole' => [
                    'scopes' => [
                        function ($q) {
                        }
                    ]
                ],
            ]
        ]);
        $this->assertCount(1, $behavior->getScopes());
    }

    public function testGetPlugins() {
        $behavior = new Behavior([
            'plugins' => [
                'test' => [
                    'class' => BehaviorTestPlugin::class,
                ]
            ]
        ]);
        $this->assertCount(1, $behavior->getPlugins());
    }

    public function testGetPluginByKey() {
        $behavior = new Behavior([
            'plugins' => [
                'test' => [
                    'class' => BehaviorTestPlugin::class,
                ]
            ]
        ]);
        $this->assertInstanceOf(BehaviorTestPlugin::class, $behavior->getPlugin('test'));
    }

    public function testGetPluginByNull() {
        $behavior = new Behavior();
        $this->assertNull($behavior->getPlugin('test'));
    }

    public function testInitPluginFromString() {
        $behavior = new Behavior([
            'plugins' => [
                'test' => BehaviorTestPlugin::class,
            ]
        ]);
        $this->assertInstanceOf(BehaviorTestPlugin::class, $behavior->getPlugin('test'));
    }

//    public function testGetScopesViaRoles() {
//        $behavior = new Behavior([
//            'roles' => [
//                'testRole' => [
//                    'scopes' => [
//                        function ($q) {
//                        }
//                    ]
//                ],
//            ]
//        ]);
//        $behavior->setRole('testRole');
//        $this->assertCount(1, $behavior->getScopes());
//    }
}

class Model extends ActiveRecord {
    public static $query = null;
    public static function find()
    {
        return self::$query;
    }
}

class BehaviorTestPlugin extends Plugin {
}