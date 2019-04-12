<?php
/**
 */

namespace execut\crudFields\fields;


use execut\crudFields\Relation;
use kartik\detail\DetailView;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\mysql\Schema;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

class RawField extends Field
{
    public $value  = null;
    public function getField() {
        return [
            'type' => DetailView::INPUT_STATIC,
            'value' => $this->value,
            'label' => $this->getLabel(),
            'displayOnly' => true,
            'format' => 'raw',
        ];
    }

    public function getColumn()
    {
        return false;
    }
}