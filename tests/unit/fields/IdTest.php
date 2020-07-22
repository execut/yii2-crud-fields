<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields;

use execut\crudFields\TestCase;
use kartik\detail\DetailView;

class IdTest extends TestCase
{
    public function testGetField()
    {
        $model = new FieldTestModel();
        $field = new Id([
            'attribute' => 'name',
            'model' => $model
        ]);
        $this->assertEquals([
            'attribute' => 'name',
            'displayOnly' => true,
            'viewModel' => $model,
            'editModel' => $model,
        ], $field->getField());
    }

    public function testDefaultId()
    {
        $field = new Id();
        $this->assertEquals('id', $field->getAttribute());
    }
}
