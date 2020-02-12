<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 6/28/17
 * Time: 10:33 AM
 */

namespace execut\crudFields;


use execut\crudFields\fields\Field;
use yii\db\ActiveQuery;

class RelationTest extends TestCase
{
    public function testGetRelationNameFromAttribute() {
        $relation = $this->getRelation();
        $this->assertEquals('testTest', $relation->name);
    }

    public function testAddWithToSearchQuery() {
        $relation = $this->getRelation();
        $relation->with = [
            'testTest2'
        ];
        $query = $this->getMockBuilder(ActiveQuery::class)
            ->setConstructorArgs(['asdasd'])
            ->setMethods(['with'])
            ->getMock();
        $query->expects($this->once())->method('with')->with([
            'testTest2'
        ])->willReturn($query);
        $relation->applyScopes($query);
    }

    public function testGetWithByDefault() {
        $relation = $this->getRelation();
        $this->assertEquals('testTest', $relation->getWith());
    }

    public function testGetColumnValue() {
        $relation = $this->getRelation();
        $this->assertEquals('test', $relation->getColumnValue($relation->field->model));
    }

    public function testGetSourceText() {
        $relation = $this->getRelation();
        $model = $relation->field->model;
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

    /**
     * @return Relation
     */
    protected function getRelation()
    {
        $model = new \execut\crudFields\fields\Model();
        $model->testTest = $model;
        $field = new Field([
            'attribute' => 'test_test_id',
            'model' => $model,
        ]);

        $relation = new Relation([
            'field' => $field,
        ]);

        return $relation;
    }
}