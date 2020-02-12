<?php
/**
 */

namespace execut\crudFields\fields;


use execut\crudFields\TestCase;
use kartik\detail\DetailView;
use kartik\grid\BooleanColumn;

class BooleanTest extends TestCase
{
    public function testGetColumn() {
        $field = new Boolean([
            'attribute' => 'visible',
        ]);
        $this->assertEquals($field->getColumn(), [
            'class' => BooleanColumn::class,
            'attribute' => 'visible',
            'label' => 'Visible'
        ]);
    }

    public function testGetField() {
        $field = new Boolean([
            'attribute' => 'visible',
        ]);
        $this->assertEquals($field->getField(), [
            'type' => DetailView::INPUT_CHECKBOX,
            'attribute' => 'visible'
        ]);
    }
}