<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\tests\unit\fields;

use execut\crudFields\fields\Textarea;
use execut\crudFields\Relation;
use execut\crudFields\TestCase;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

class TextareaTest extends \Codeception\Test\Unit
{
    public function testGetColumn()
    {
        $model = new FieldTestModel();
        $field = new Textarea([
            'attribute' => 'name',
            'model' => $model
        ]);
        $this->assertEquals([
            'attribute' => 'name',
            'label' => 'Name',
        ], $field->getColumn());
    }
}
