<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\tests\unit\relation;

use execut\crudFields\relation\UrlMaker;
use execut\crudFields\TestCase;
use execut\crudFields\tests\unit\fields\FieldTestModel;

class UrlMakerTest extends \Codeception\Test\Unit
{
    public function testGetUpdateUrlWithoutUrl()
    {
        $urlMaker = new UrlMaker();
        $model = new FieldTestModel();
        $this->assertNull($urlMaker->make($model, ''));
    }

    public function testGetUpdateUrlWithArrayUrl()
    {
        $urlMaker = new UrlMaker([
            'test',
            'id' => 1,
        ]);
        $model = new FieldTestModel();
        $this->assertEquals([
            'test/update',
            'id' => 1,
        ], $urlMaker->make($model, ''));
    }

    public function testGetUpdateUrlWithIdCalculation()
    {
        $urlMaker = new UrlMaker([
            'test',
        ]);
        $model = new FieldTestModel();
        $model->id = 1;
        $this->assertEquals([
            'test/update',
            'id' => 1,
        ], $urlMaker->make($model, 'id'));
    }

    public function testGetUpdateUrlWithBadAttribute()
    {
        $urlMaker = new UrlMaker([
            'test',
        ]);
        $model = new FieldTestModel();
        $model->badAttribute = 1;
        $this->assertNull($urlMaker->make($model, 'badAttribute'));
    }

    public function testGetUpdateUrlWithPkArrayCalculation()
    {
        $urlMaker = new UrlMaker([
            'test',
        ]);
        $model = new FieldTestModel();
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

    public function testGetUrl()
    {
        $maker = new UrlMaker(['test']);
        $this->assertEquals(['test'], $maker->getUrl());
    }

    public function testGetUpdateUrl()
    {
        $maker = new UrlMaker(null, ['test']);
        $this->assertEquals(['test'], $maker->getUpdateUrl());
    }

    public function testMakeFromUpdateUrl()
    {
        $maker = new UrlMaker(null, ['test']);
        $model = new FieldTestModel();
        $this->assertEquals(['test'], $maker->make($model, 'id'));
    }

    public function testGetIsNoRenderRelationLink()
    {
        $maker = new UrlMaker(null, null, true);
        $this->assertTrue($maker->getIsNoRenderRelationLink());
    }

    public function testMakeWhenIsNoRenderRelationLink()
    {
        $maker = new UrlMaker(null, ['test'], true);
        $model = new FieldTestModel();
        $this->assertNull($maker->make($model, 'id'));
    }
}
