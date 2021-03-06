<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\tests\unit\fields;

use execut\crudFields\fields\Boolean;
use kartik\detail\DetailView;
use kartik\grid\BooleanColumn;

class BooleanTest extends \Codeception\Test\Unit
{
    public function testGetColumn()
    {
        $field = new Boolean([
            'attribute' => 'visible',
        ]);
        $this->assertEquals($field->getColumn(), [
//            'class' => BooleanColumn::class,
            'filter' => [
                'Нет',
                'Да',
            ],
            'attribute' => 'visible',
            'label' => 'Visible'
        ]);
    }

    public function testGetField()
    {
        $field = new Boolean([
            'attribute' => 'visible',
        ]);
        $this->assertEquals($field->getField(), [
            'type' => DetailView::INPUT_CHECKBOX,
            'attribute' => 'visible'
        ]);
    }
}
