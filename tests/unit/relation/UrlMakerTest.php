<?php


namespace execut\crudFields\relation;


use execut\crudFields\TestCase;

class UrlMakerTest extends TestCase
{
    public function testGetUpdateUrlWithoutUrl() {
        $urlMaker = new UrlMaker();
        $model = new \execut\crudFields\fields\FieldTestModel();
        $this->assertNull($urlMaker->make($model, ''));
    }

    public function testGetUpdateUrlWithArrayUrl() {
        $urlMaker = new UrlMaker([
            'test',
            'id' => 1,
        ]);
        $model = new \execut\crudFields\fields\FieldTestModel();
        $this->assertEquals([
            'test/update',
            'id' => 1,
        ], $urlMaker->make($model, ''));
    }

    public function testGetUpdateUrlWithIdCalculation() {
        $urlMaker = new UrlMaker([
            'test',
        ]);
        $model = new \execut\crudFields\fields\FieldTestModel();
        $model->id = 1;
        $this->assertEquals([
            'test/update',
            'id' => 1,
        ], $urlMaker->make($model, 'id'));
    }

    public function testGetUpdateUrlWithBadAttribute() {
        $urlMaker = new UrlMaker([
            'test',
        ]);
        $model = new \execut\crudFields\fields\FieldTestModel();
        $model->badAttribute = 1;
        $this->assertNull($urlMaker->make($model, 'badAttribute'));
    }

    public function testGetUpdateUrlWithPkArrayCalculation() {
        $urlMaker = new UrlMaker([
            'test',
        ]);
        $model = new \execut\crudFields\fields\FieldTestModel();
        $model->id = [
            'pk1' => 'pk1_value',
            'pk2' => 'pk2_value',
        ];
        $this->assertEquals([
            'test/update',
            'pk1' => 'pk1_value',
            'pk2' => 'pk2_value',
        ], $urlMaker->make($model, 'id'));
    }

    public function testGetUrl() {
        $maker = new UrlMaker(['test']);
        $this->assertEquals(['test'], $maker->getUrl());
    }

    public function testGetUpdateUrl() {
        $maker = new UrlMaker(null, ['test']);
        $this->assertEquals(['test'], $maker->getUpdateUrl());
    }

    public function testMakeFromUpdateUrl() {
        $maker = new UrlMaker(null, ['test']);
        $model = new \execut\crudFields\fields\FieldTestModel();
        $this->assertEquals(['test'], $maker->make($model, 'id'));
    }

    public function testGetIsNoRenderRelationLink() {
        $maker = new UrlMaker(null, null, true);
        $this->assertTrue($maker->getIsNoRenderRelationLink());
    }

    public function testMakeWhenIsNoRenderRelationLink() {
        $maker = new UrlMaker(null, ['test'], true);
        $model = new \execut\crudFields\fields\FieldTestModel();
        $this->assertNull($maker->make($model, 'id'));
    }
}