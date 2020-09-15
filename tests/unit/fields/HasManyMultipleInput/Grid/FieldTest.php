<?php

/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

namespace execut\crudFields\tests\unit\fields\HasManyMultipleInput\Grid;

use Codeception\Test\Unit;
use execut\crudFields\fields\Action;
use execut\crudFields\fields\HasManyMultipleInput\Grid\Field;
use execut\crudFields\models\AllFields;
use execut\crudFields\Relation;
use execut\crudFields\tests\unit\Model;
use execut\crudFields\widgets\TestGridViewWidget;
use yii\base\Controller;
use yii\base\Module;
use yii\base\Widget;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveQueryInterface;
use yii\web\Request;

class FieldTest extends Unit
{
    protected $oldController = null;
    protected function _before()
    {
        parent::_before();
        $module = new Module('test');
        $controller = new Controller('test', $module);
        $this->oldController = \yii::$app->controller;
        \yii::$app->controller = $controller;
        \yii::$app->setModules([
            'gridview' => [
                'class' => '\kartik\grid\Module',
            ],
        ]);
        \yii::$app->setComponents([
            'request' => [
                'class' => Request::class,
                'cookieValidationKey' => 'wefJDF8sfdsfSDefwqdxj9oq',
                'scriptUrl' => '/index.php',
                'url' => 'test',
            ],
        ]);
    }

    protected function _after()
    {
        parent::_after();
        \yii::$app->controller = $this->oldController;
        TestGridViewWidget::$factConfig = null;
    }


    public function testRender()
    {
        $grid = new Field(function () {
            return [
                'class' => TestGridViewWidget::class,
            ];
        });

        $relationObject = $this->getMockBuilder(Relation::class)
            ->getMock();
        $relationObject->method('getName')
            ->willReturn('testRelationName');
        $model = new Model();
        $query = $this->getMockBuilder(ActiveQueryInterface::class)
            ->getMock();
        $model->setRelation('testRelationName', $query);
        $relationObject->method('getRelationModel')
            ->willReturn($model);

        $this->assertEquals('test', $grid->render($relationObject, $model));
        $result = TestGridViewWidget::$factConfig;
        $this->assertIsArray($result);
        $this->assertArrayHasKey('columns', $result);
        $this->assertEquals([
            'testAttribute' => [
                'attribute' => 'testAttribute'
            ],
            'actions' => [
                'class' => Action::class,
            ]
        ], $result['columns']);
        unset($result['columns']);

        $this->assertArrayHasKey('dataProvider', $result);
        $this->assertInstanceOf(ActiveDataProvider::class, $result['dataProvider']);
        $dp = $result['dataProvider'];
        unset($result['dataProvider']);
        $this->assertEquals(spl_object_hash($dp->query), spl_object_hash($query));

        $this->assertEquals([
            'layout' => '{summary}{items}{pager}',
            'bordered' => false,
            'showOnEmpty' => true,
        ], $result);
    }
}
