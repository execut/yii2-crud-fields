<?php


namespace execut\crudFields;

use \execut\crudFields\fields\FieldTestModel;
class LinkRendererTest extends \Codeception\Test\Unit
{
    public function testRenderWithEmptyName() {
        $model = new FieldTestModel();
        $model->test_test_id = 2;
        $model->name = null;

        $renderer = new LinkRenderer($model, 'name', 'test_test_id');

        $this->assertEquals('2', $renderer->render());
    }

    public function testRenderWithoutIdAttribute() {
        $model = new FieldTestModel();
        $model->name = 'test';

        $renderer = new LinkRenderer($model, 'name');

        $this->assertEquals('test', $renderer->render());
    }

    public function testRenderWithoutUrlMaker() {
        $model = new FieldTestModel();
        $model->name = 'test';

        $renderer = new LinkRenderer($model, 'name', 'test_test_id');
        $this->assertEquals('test', $renderer->render());
    }

    public function testRenderByAttribute() {
        $model = new FieldTestModel();
        $model->name = 'test';
        $model->test_test_id = 3;

        $renderer = new LinkRenderer($model, 'name', 'test_test_id', null, ['/test/test/update']);

        $this->assertEquals('test&nbsp;<a href="/test/test/update" title="Перейти к редактированию">>>></a>', $renderer->render());
    }

    public function testRenderByAttributeWithLabel() {
        $model = new FieldTestModel();
        $model->name = 'test';
        $model->test_test_id = 3;

        $renderer = new LinkRenderer($model, 'name', 'test_test_id', 'test label', ['/test/test/update']);

        $this->assertEquals('test&nbsp;<a href="/test/test/update" title="test label - перейти к редактированию">>>></a>', $renderer->render());
    }

    public function testGetModel() {
        $model = new FieldTestModel();
        $renderer = new LinkRenderer($model);
        $this->assertEquals($model, $renderer->getModel());
    }

    public function testSetModel() {
        $model = new FieldTestModel();
        $renderer = new LinkRenderer();
        $this->assertEquals($renderer, $renderer->setModel($model));
        $this->assertEquals($model, $renderer->getModel());
    }

    public function testGetNameAttribute() {
        $renderer = new LinkRenderer(null, 'test');
        $this->assertEquals('test', $renderer->getNameAttribute());
    }

    public function testSetNameAttribute() {
        $renderer = new LinkRenderer();
        $name = 'test';
        $this->assertEquals($renderer, $renderer->setNameAttribute($name));
        $this->assertEquals($name, $renderer->getNameAttribute());
    }

    public function testGetIdAttribute() {
        $idAttribute = 'test';
        $renderer = new LinkRenderer(null, null, $idAttribute);
        $this->assertEquals($idAttribute, $renderer->getIdAttribute());
    }

    public function testSetIdAttribute() {
        $idAttribute = 'test';
        $renderer = new LinkRenderer();
        $this->assertEquals($renderer, $renderer->setIdAttribute($idAttribute));
        $this->assertEquals($idAttribute, $renderer->getIdAttribute());
    }

    public function testTryWithoutModelRenderException() {
        $renderer = new LinkRenderer();
        $this->expectExceptionMessage('Model is required for render');
        $renderer->render();
    }

    public function testTryWithoutNameAttributeRenderException() {
        $model = new FieldTestModel();
        $renderer = new LinkRenderer($model);
        $this->expectExceptionMessage('nameAttribute is required for render');
        $renderer->render();
    }

    public function testGetLabel() {
        $label = 'test';
        $renderer = new LinkRenderer(null, null, null, $label);
        $this->assertEquals($label, $renderer->getLabel());
    }

    public function testSetLabel() {
        $renderer = new LinkRenderer();
        $name = 'test';
        $this->assertEquals($renderer, $renderer->setLabel($name));
        $this->assertEquals($name, $renderer->getLabel());
    }

    public function testGetUrl() {
        $url = ['test'];
        $renderer = new LinkRenderer(null, null, null, null, $url);
        $this->assertEquals($url, $renderer->getUrl());
    }

    public function testSetUrl() {
        $url = ['test'];
        $renderer = new LinkRenderer();
        $this->assertEquals($renderer, $renderer->setUrl($url));
        $this->assertEquals($url, $renderer->getUrl());
    }
}