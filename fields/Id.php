<?php
/**
 */

namespace execut\crudFields\fields;

use kartik\daterange\DateRangePicker;
use kartik\detail\DetailView;
use yii\helpers\ArrayHelper;

class Id extends Field
{
    public $attribute = 'id';
    public function getField()
    {
        return array_merge(parent::getField(), [
            'displayOnly' => true,
        ]);
    }
}