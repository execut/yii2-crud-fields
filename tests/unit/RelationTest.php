<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 6/28/17
 * Time: 10:33 AM
 */

namespace execut\crudFields;


use execut\crudFields\fields\Field;
use execut\crudFields\LinkRenderer;
use execut\crudFields\relation\UrlMaker;
use yii\base\Model;
use yii\db\ActiveQuery;

class RelationTest extends \Codeception\Test\Unit
{
    public function testGetRelationNameFromAttribute() {
        $relation = new Relation([
            'attribute' => 'test_test_id',
        ]);
        $this->assertEquals('testTest', $relation->name);
    }

    public function testGetRelationQueryIsNull() {
        $relation = new Relation();
        $this->assertNull($relation->getRelationQuery());
    }

    public function testAddWithToSearchQuery() {
        $relation = new Relation();
        $relation->with = [
            'testTest2'
        ];
        $query = $this->getMockBuilder(ActiveQuery::class)
            ->setConstructorArgs(['asdasd'])
            ->getMock();
        $query->expects($this->once())->method('with')->with([
            'testTest2'
        ])->willReturn($query);
        $relation->applyScopes($query);
    }

    public function testGetWithByDefault() {
        $relation = new Relation([
            'name' => 'testTest',
        ]);
        $this->assertEquals('testTest', $relation->getWith());
    }

    public function testGetColumnValue() {
        $model = new fields\FieldTestModel();
        $model->testTest = $model;
        $relation = new Relation([
            'name' => 'testTest',
            'nameAttribute' => 'name',
            'isNoRenderRelationLink' => true,
            'model' => $model,
        ]);
        $this->assertEquals('test', $relation->getColumnValue($model));
    }

    public function testGetSourceText() {
        $model = new fields\FieldTestModel();
        $model->testTest = $model;

        $relation = new Relation([
//            'url' => ['test/test'],
            'attribute' => 'test_test_id',
            'model' => $model,
            'name' => 'testTest',
            'nameAttribute' => 'name',
            'value' => true,
        ]);

        $q = $this->getMockBuilder(ActiveQuery::class)
            ->setConstructorArgs([get_class($model)])
            ->getMock();
        $q->method('andWhere')
            ->willReturn($q);
        $q->method('all')
            ->willReturn([$model]);
        $model::$subQuery->link = [
            'id' => 'id',
        ];
        $model::$query = $q;

        $this->assertEquals('test', $relation->getSourceText());
    }

    public function testGetValueNull() {
        $relation = new Relation();
        $this->assertNull($relation->getValue());
    }

    public function testGetValue() {
        $relation = new Relation([
            'value' => true,
        ]);
        $this->assertTrue($relation->getValue());
    }

    public function testGetValueViaField() {
        $field = $this->getMockBuilder(Field::class)->getMock();
        $field->method('getValue')
            ->willReturn(true);
        $relation = new Relation([
            'field' => $field,
        ]);
        $this->assertTrue($relation->getValue());
    }

    /**
     * @return Relation
     */
    protected function getRelation()
    {
        $model = new fields\FieldTestModel();
        $model->testTest = $model;
        $field = new Field([
            'url' => ['test/test'],
            'attribute' => 'test_test_id',
            'model' => $model,
            'relation' => 'testTest',
        ]);

        $relation = $field->getRelationObject();

        return $relation;
    }

    public function testGetDefaultUrlMaker() {
        $relation = new Relation([
            'url' => ['test'],
            'updateUrl' => ['updateUrl'],
            'isNoRenderRelationLink' => true,
        ]);
        $urlMaker = $relation->getUrlMaker();
        $this->assertInstanceOf(UrlMaker::class, $urlMaker);
        $this->assertEquals(['test'], $urlMaker->getUrl());
        $this->assertEquals(['updateUrl'], $urlMaker->getUpdateUrl());
        $this->assertTrue($urlMaker->getIsNoRenderRelationLink());
    }

    public function testGetUpdateUrlParamsViaUrlMaker() {
        $urlMaker = $this->getMockBuilder(UrlMaker::class)->getMock();
        $model = new fields\FieldTestModel();
        $urlMaker->expects($this->once())
            ->method('make')
            ->with($model, 'id')
            ->willReturn(['test']);

        $relation = new Relation([
            'model' => $model,
            'urlMaker' => $urlMaker,
            'idAttribute' => 'id',
        ]);

        $this->assertEquals(['test'], $relation->getUpdateUrlParamsForModel($model));
    }

    public function testSetLinkRenderer() {
        $renderer = new LinkRenderer();
        $relation = new Relation([
            'linkRenderer' => $renderer,
        ]);
        $this->assertEquals(spl_object_hash($renderer), spl_object_hash($relation->getLinkRenderer()));
    }

    public function testGetLink() {
        $model = new fields\FieldTestModel();
        $nameAttribute = 'test name attribute';
        $renderer = $this->getMockBuilder(LinkRenderer::class)->onlyMethods(['render'])->getMock();
        $renderer->expects($this->once())
            ->method('render')
            ->willReturn('test');

        /**
         * @var Relation $relation
         */
        $relation = $this->getMockBuilder(Relation::class)->onlyMethods(['configureLinkRenderer'])->getMock();

        $relation->expects($this->once())
            ->method('configureLinkRenderer')
            ->with($model, $nameAttribute)
            ->willReturn($renderer);

        $link = $relation->getLink($model, $nameAttribute);
        $this->assertEquals('test', $link);
    }

    public function testConfigureLinkRenderer() {
        $testUrl = ['test'];
        $model = new fields\FieldTestModel();
        $nameAttribute = 'test name attribute';
        $idAttribute = 'test id attribute';

        $urlMaker = $this->getMockBuilder(UrlMaker::class)->getMock();
        $urlMaker->expects($this->once())
            ->method('make')
            ->willReturn($testUrl);

        $renderer = $this->getMockBuilder(LinkRenderer::class)->getMock();
        $renderer->expects($this->once())
            ->method('setUrl')
            ->with($testUrl)
            ->willReturn($renderer);
        $renderer->expects($this->once())
            ->method('setModel')
            ->with($model)
            ->willReturn($renderer);
        $renderer->expects($this->once())
            ->method('setNameAttribute')
            ->with($nameAttribute)
            ->willReturn($renderer);
        $renderer->expects($this->once())
            ->method('setIdAttribute')
            ->with($idAttribute)
            ->willReturn($renderer);

        $relation = new Relation([
            'linkRenderer' => $renderer,
            'urlMaker' => $urlMaker,
            'url' => $testUrl,
            'idAttribute' => $idAttribute,
            'attribute' => $idAttribute,
        ]);

        $relation->configureLinkRenderer($model, $nameAttribute);
    }

    public function testGetIdAttribute() {
        $idAttribute = 'test';
        $relation = new Relation([
            'idAttribute' => $idAttribute,
        ]);
        $this->assertEquals($idAttribute, $relation->getIdAttribute());
    }

    public function testGetIdAttributeCalculateForHasMany() {
        $model = new fields\FieldTestModel();

        $relation = new Relation([
            'model' => $model,
            'name' => 'testTests',
            'attribute' => 'test_test_id',
        ]);
        $this->assertEquals('test_test_id', $relation->getIdAttribute());
    }

    public function testGetIdAttributeCalculateForDirectSetWhenExistedRelation() {
        $model = new fields\FieldTestModel();

        $relation = new Relation([
            'model' => $model,
            'name' => 'testTests',
            'attribute' => 'test_test_id',
            'idAttribute' => 'test',
        ]);
        $this->assertEquals('test', $relation->getIdAttribute());
    }

//    public function testConfigureLinkRenderer() {
//        $renderer = $this->getMockBuilder(LinkRenderer::class)->getMock();
//        $renderer->method('')
//    }
}