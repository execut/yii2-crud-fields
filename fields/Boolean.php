<?php
/**
 */

namespace execut\crudFields\fields;


use kartik\detail\DetailView;
use kartik\grid\BooleanColumn;
use yii\helpers\ArrayHelper;

class Boolean extends Field
{
    public function getColumn()
    {
        return ArrayHelper::merge([
            'class' => BooleanColumn::class,
        ], parent::getColumn());
    }

    public function getField()
    {
        return array_merge(parent::getField(), [
            'type' => DetailView::INPUT_CHECKBOX,
        ]);
    }
}