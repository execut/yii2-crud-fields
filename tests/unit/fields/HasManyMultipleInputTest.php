<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\tests\unit\fields;

use execut\crudFields\fields\Field;
use execut\crudFields\models\AllFields;
use execut\crudFields\fields\HasManyMultipleInput;
use execut\crudFields\Relation;
use execut\crudFields\widgets\TestGridViewWidget;
use execut\crudFields\tests\unit\Model;
use kartik\grid\GridView;
use unclead\multipleinput\MultipleInput;
use yii\base\Exception;
use yii\base\Module;
use yii\db\ActiveQuery;
use yii\web\Controller;
use yii\web\Request;

class HasManyMultipleInputTest extends \Codeception\Test\Unit
{
    protected $oldController = null;
    protected $oldRequest = null;
    protected function _before()
    {
        $this->oldController = \yii::$app->controller;
        $this->oldRequest = \yii::$app->request;
        $module = new Module('test_module');
        $controller = new Controller('test_controller', $module);
        \yii::$app->controller = $controller;
        \yii::$app->setComponents([
            'request' => new Request([
                'url' => 'test',
            ])
        ]);
    }

    protected function _after()
    {
        \yii::$app->controller = $this->oldController;
        \yii::$app->setComponents([
            'request' => $this->oldRequest,
        ]);
    }

    public function testGetFields()
    {
        $field = new HasManyMultipleInput([
            'field' => false,
        ]);
        $this->assertEquals([], $field->getFields());
    }

    /**
     * ========================================
     * Grid tests
     * ========================================
     */
    protected function getTestModel($isVia = false)
    {
        $firstModel = new AllFields();
        $firstModel->attributes = [
            'name' => 'First model',
        ];
        $secondModel = new AllFields();
        $secondModel->attributes = [
            'name' => 'Second model',
        ];
        $model = new AllFields();
        $model->scenario = Field::SCENARIO_FORM;
        $model->name = 'Test';
        if ($isVia) {
            $model->hasManyMultipleinputVia = [
                $firstModel,
                $secondModel,
            ];
        } else {
            $model->hasManyMultipleinput = [
                $firstModel,
                $secondModel,
            ];
        }

        if (!$model->save()) {
            throw new Exception('Failed to save model. Validation errors: ' . var_export($model->errors, true));
        }

        return $model;
    }

    public function testGetFieldGridByDefault()
    {
        $field = new HasManyMultipleInput();
        $this->assertInstanceOf(HasManyMultipleInput\Grid\Field::class, $field->getFieldGrid());
    }

    public function testGetFieldAsGrid()
    {
        $model = $this->getTestModel();
        $grid = $this->getMockBuilder(HasManyMultipleInput\Grid\Grid::class)
            ->getMock();
        $field = new HasManyMultipleInput([
            'isGridForOldRecords' => true,
            'model' => $model,
            'attribute' => 'hasManyMultipleinput',
            'relation' => 'hasManyMultipleinput',
            'relationQuery' => $model->getRelation('hasManyMultipleinput'),
            'label' => 'Label',
            'fieldGrid' => $grid,
        ]);
        $grid->expects($this->once())
            ->method('render')
            ->with($field->getRelationObject(), $model)
            ->willReturn('test');
        $fields = $field->getFields();
        $this->assertEquals([
            'hasManyMultipleinputGroup' => [
                'group' => true,
                'label' => 'Label',
                'groupOptions' => [
                    'class' => 'success',
                ],
            ],
            'hasManyMultipleinput' => [
                'value' => '',
                'format' => 'raw',
                'displayOnly' => true,
                'group' => true,
                'groupOptions' => [
                    'style' => [
                        'padding' => 0,
                    ],
                ],
                'label' => function () {},
            ],
        ], $fields);
        $label = $fields['hasManyMultipleinput']['label'];
        $this->assertIsCallable($label);

        $gridHtml = $label($model);
        $this->assertEquals('test', $gridHtml);
    }

    public function testGetFieldForHasManyRelation()
    {
        $relationObject = $this->getMockBuilder(Relation::class)->getMock();
        $model = new HasOneSelect2TestModel;
        $relationObject->method('getRelationModel')
            ->with(true)
            ->willReturn($model);
        $field = new HasManyMultipleInput([
            'relationObject' => $relationObject,
            'attribute' => 'name',
            'model' => $model,
        ]);
        $field = $field->getField();
        $this->assertEquals([
            'type' => 'widget',
            'attribute' => 'name',
            'format' => 'raw',
            'value' => '',
            'widgetOptions' => [
                'class' => 'unclead\multipleinput\MultipleInput',
                'allowEmptyList' => true,
                'model' => $model,
                'addButtonPosition' => 'header',
                'columns' => [
                    'id' => [
                        'type' => 'hiddenInput',
                        'name' => 'id'
                    ],
                ],
            ],
            'viewModel' => $model,
            'editModel' => $model,
        ], $field);
    }

    public function testGetFieldForHasManyRelationVia()
    {
        $model = $this->getTestModel(true);
        $field = new HasManyMultipleInput([
            'relation' => 'hasManyMultipleinputVia',
            'attribute' => 'hasManyMultipleinputVia',
            'relationQuery' => $model->getRelation('hasManyMultipleinputVia'),
            'model' => $model,
            'viaColumns' => [
                'direct_defined_column' => [
                    'name' => 'direct_defined_column_name',
                ]
            ]
        ]);
        $field = $field->getField();

        $this->assertArrayHasKey('widgetOptions', $field);
        $this->assertArrayHasKey('model', $field['widgetOptions']);
        $relationModel = $field['widgetOptions']['model'];
        unset($field['widgetOptions']['model']);
        $this->assertInstanceOf(AllFields\Nested::class, $relationModel);

        $this->assertArrayHasKey('widgetOptions', $field);
        $this->assertArrayHasKey('columns', $field['widgetOptions']);
        $this->assertEquals([
            'name',
            'bool',
            'hasOne',
            'periodically_updated',
            'periodicallyUpdatedWidget',
            'record_for_update_when_a_specific_value_selected',
            'direct_defined_column',
        ], array_keys($field['widgetOptions']['columns']));

        $columns = [
            'name' => $field['widgetOptions']['columns']['name'],
            'direct_defined_column' => $field['widgetOptions']['columns']['direct_defined_column'],
        ];
        $field['widgetOptions']['columns'] = $columns;

        $this->assertEquals([
            'type' => 'widget',
            'attribute' => 'hasManyMultipleinputVia',
            'format' => 'raw',
            'value' => '',
            'widgetOptions' => [
                'class' => 'unclead\multipleinput\MultipleInput',
                'allowEmptyList' => true,
                'addButtonPosition' => 'header',
                'columns' => [
                    'name' => [
                        'type' => 'textInput',
                        'name' => 'name',
                        'enableError' => true,
                        'options' => [
                            'placeholder' => 'Name',
                        ],
                        'title' => 'Name',
                    ],
                    'direct_defined_column' => [
                        'name' => 'direct_defined_column_name',
                        'title' => '<label for="nested-direct_defined_column_name">Direct Defined Column Name</label>',
                    ]
                ],
            ],
            'viewModel' => $model,
            'editModel' => $model,
        ], $field);
    }

    public function testApplyScopesWithFalseScope()
    {
        $relationObject = $this->getMockBuilder(Relation::class)
            ->setConstructorArgs([])
            ->onlyMethods(['applyScopeIsExistRecords'])
            ->getMock();
        $relationObject->expects($this->never())
            ->method('applyScopeIsExistRecords');
        $field = new HasManyMultipleInput([
            'relationObject' => $relationObject,
            'scope' => false,
        ]);
        $query = new ActiveQuery('s');

        $this->assertEquals($query, $field->applyScopes($query));
    }

    public function testApplyScopesWithValidationErrors()
    {
        $relationObject = $this->getMockBuilder(Relation::class)
            ->setConstructorArgs([])
            ->onlyMethods(['applyScopeIsExistRecords'])
            ->getMock();
        $relationObject->expects($this->never())
            ->method('applyScopeIsExistRecords');
        $model = new Model();
        $model->addError('id', 'error');
        $field = new HasManyMultipleInput([
            'relationObject' => $relationObject,
            'model' => $model,
        ]);
        $query = new ActiveQuery('s');

        $this->assertEquals($query, $field->applyScopes($query));
        $this->assertEquals('false', $query->where);
    }

    public function testApplyScopesWithEmptyValue()
    {
        $relationObject = $this->getMockBuilder(Relation::class)
            ->setConstructorArgs([])
            ->onlyMethods(['applyScopeIsExistRecords'])
            ->getMock();
        $relationObject->expects($this->once())
            ->method('applyScopeIsExistRecords')
            ->with($this->isInstanceOf(ActiveQuery::class));
        $model = new Model();
        $field = new HasManyMultipleInput([
            'relationObject' => $relationObject,
            'model' => $model,
            'value' => [],
        ]);
        $query = new ActiveQuery('s');

        $this->assertEquals($query, $field->applyScopes($query));
    }

    public function testApplyScopesWithEmptyModel()
    {
        $relationObject = $this->getMockBuilder(Relation::class)
            ->setConstructorArgs([])
            ->onlyMethods(['applyScopeIsExistRecords'])
            ->getMock();
        $relationObject->expects($this->once())
            ->method('applyScopeIsExistRecords')
            ->with($this->isInstanceOf(ActiveQuery::class));
        $model = new Model();
        $field = new HasManyMultipleInput([
            'relationObject' => $relationObject,
            'model' => $model,
            'value' => [
                new Model(),
            ],
        ]);
        $query = new ActiveQuery('s');

        $this->assertEquals($query, $field->applyScopes($query));
    }

    public function testApplyScopesWithModel()
    {
        $model = new AllFields();
        $model->scenario = Field::SCENARIO_FORM;

        $valueModel = new AllFields();
        $valueModel->scenario = Field::SCENARIO_FORM;
        $valueModel->name = 'test';
        $model->hasManyMultipleinput = [
            $valueModel,
        ];
        $field = new HasManyMultipleInput([
            'relation' => 'hasManyMultipleinput',
            'attribute' => 'hasManyMultipleinput',
            'relationQuery' => $model->getRelation('hasManyMultipleinput'),
            'model' => $model,
            'value' => [
                $valueModel,
            ],
        ]);
        $query = new ActiveQuery('s');

        $this->assertEquals($query, $field->applyScopes($query));
        $this->assertArrayHasKey('example_all_fields.id', $query->where);
        $subQuery = $query->where['example_all_fields.id'];
        $this->assertEquals([
            'ILIKE',
            'name',
            'test'
        ], $subQuery->where);
    }

    public function testGetColumnFalse()
    {
        $field = new HasManyMultipleInput([
            'column' => false,
        ]);
        $this->assertFalse($field->getColumn());
    }

    public function testGetColumn()
    {
        $model = new \stdClass();
        $relationObject = $this->getMockBuilder(Relation::class)->getMock();
        $relationObject->expects($this->once())
            ->method('getColumnValue')
            ->with($model)
            ->willReturn(1);
        $field = new HasManyMultipleInput([
            'attribute' => 'test_attribute',
            'column' => [
                'filter' => false,
            ],
            'relationObject' => $relationObject,
        ]);
        $column = $field->getColumn();
        $this->assertEquals([
            'attribute' => 'test_attribute',
            'format' => 'html',
            'value' => function () {
            },
            'filter' => false,
            'label' => 'Test Attribute',
        ], $column);
        $value = $column['value'];
        $this->assertIsCallable($value);
        $this->assertEquals(1, $value($model));
    }


    public function testGetColumnGridByDefault()
    {
        $field = new HasManyMultipleInput();
        $this->assertInstanceOf(HasManyMultipleInput\Grid\Column::class, $field->getColumnGrid());
    }

    public function testGetColumnAsGrid()
    {
        $model = $this->getTestModel();
        $grid = $this->getMockBuilder(HasManyMultipleInput\Grid\Grid::class)
            ->getMock();
        $field = new HasManyMultipleInput([
            'relation' => 'hasManyMultipleinput',
            'attribute' => 'hasManyMultipleinput',
            'relationQuery' => $model->getRelation('hasManyMultipleinput'),
            'model' => $model,
            'columnGrid' => $grid,
            'column' => [
                'filter' => false,
            ],
            'isGridInColumn' => true,
        ]);
        $grid->expects($this->once())
            ->method('render')
            ->with($field->getRelationObject(), $model)
            ->willReturn('test');


        $column = $field->getColumn();
        $this->assertArrayHasKey('value', $column);
        $callback = $column['value'];
        $this->assertIsCallable($callback);
        $result = $callback($model);
        $this->assertStringContainsString('test', $result);
    }

    public function testGetColumnWithHasRelationFilter()
    {
        $model = new AllFields();
        $model->scenario = Field::SCENARIO_FORM;
        $field = new HasManyMultipleInput([
            'isHasRelationAttribute' => 'hasManyMultipleinput_hasRelation',
            'model' => $model,
            'attribute' => 'hasManyMultipleinput',
            'relation' => 'hasManyMultipleinput',
            'relationQuery' => $model->getRelation('hasManyMultipleinput'),
        ]);
        $column = $field->getColumn();

        $this->assertArrayHasKey('filter', $column);
        $filter = $column['filter'];
        $this->assertStringContainsString(<<<HTML
class="has-relation-dropdown"><select id="allfields-hasmanymultipleinput_hasrelation" class="inored-input" name="AllFields[hasManyMultipleinput_hasRelation]">
<option value=""></option>
<option value="0">Нет</option>
<option value="1">Есть</option>
</select></div>
HTML, $filter);
    }

    public function testGetColumnWithHasManyFilter()
    {
        $model = new AllFields();
        $model->scenario = Field::SCENARIO_FORM;
        $field = new HasManyMultipleInput([
            'isRenderFilter' => true,
            'isHasRelationAttribute' => 'hasManyMultipleinput_hasRelation',
            'model' => $model,
            'attribute' => 'hasManyMultipleinput',
            'relation' => 'hasManyMultipleinput',
            'relationQuery' => $model->getRelation('hasManyMultipleinput'),
        ]);
        $column = $field->getColumn();

        $this->assertArrayHasKey('filter', $column);
        $filter = $column['filter'];
        $this->assertStringContainsString('AllFields[hasManyMultipleinput]', $filter);
        $this->assertStringContainsString(<<<HTML
class="has-relation-dropdown"><select id="allfields-hasmanymultipleinput_hasrelation" class="inored-input" name="AllFields[hasManyMultipleinput_hasRelation]">
<option value=""></option>
<option value="0">Нет</option>
<option value="1">Есть</option>
</select></div>
HTML, $filter);
    }

    public function testGetMultipleInputField()
    {
        $model = $this->getTestModel();
        $field = new HasManyMultipleInput([
            'attribute' => 'hasManyMultipleinput',
            'relation' => 'hasManyMultipleinput',
            'relationQuery' => $model->getRelation('hasManyMultipleinput'),
            'model' => $model,
            'defaultMultipleInputColumnConfig' => [
                'defaultMultipleInputColumnConfigKey' => 'defaultMultipleInputColumnConfigValue',
            ],
        ]);
        $multipleInputField = $field->getMultipleInputField();
        $this->assertArrayHasKey('options', $multipleInputField);
        $this->assertArrayHasKey('model', $multipleInputField['options']);
        $this->assertInstanceOf(AllFields\Nested::class, $multipleInputField['options']['model']);
        unset($multipleInputField['options']['model']);
        $this->assertArrayHasKey('columns', $multipleInputField['options']);
        $columns = [
            'id' => $multipleInputField['options']['columns']['id']
        ];
        $multipleInputField['options']['columns'] = $columns;

        $this->assertEquals([
            'name' => 'hasManyMultipleinput',
            'type' => MultipleInput::class,
            'enableError' => true,
            'options' => [
                'class' => 'unclead\multipleinput\MultipleInput',
                'allowEmptyList' => true,
                'addButtonPosition' => 'header',
                'columns' => [
                    'id' => [
                        'defaultMultipleInputColumnConfigKey' => 'defaultMultipleInputColumnConfigValue',
                        'type' => 'hiddenInput',
                        'name' => 'id',
                    ],
                ],
            ],
            'title' => 'HasManyMultipleinput',
        ], $multipleInputField);
    }

    public function testGetMultipleInputFieldDisabled()
    {
        $field = new HasManyMultipleInput([
            'multipleInputField' => false,
        ]);
        $this->assertFalse($field->getMultipleInputField());
    }
}
