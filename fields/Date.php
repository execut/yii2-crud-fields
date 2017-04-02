<?php
/**
 */

namespace execut\crudFields\fields;


use kartik\daterange\DateRangePicker;
use kartik\detail\DetailView;
use yii\helpers\ArrayHelper;

class Date extends Field
{
    public function getColumn()
    {
        return ArrayHelper::merge([
            'filter' => DateRangePicker::widget([
                'attribute' => $this->attribute,
                'model' => $this->model,
                'convertFormat'=>true,
                'pluginOptions'=>[
                    'timePicker'=>true,
                    'timePickerIncrement'=>15,
                    'locale'=>['format'=>'Y-m-d']
                ]
            ]),
        ], parent::getColumn());
    }

    public function getField()
    {
        return array_merge(parent::getField(), [
            'displayOnly' => true,
        ]);
    }
}