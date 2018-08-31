<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 8/31/18
 * Time: 12:44 PM
 */

namespace execut\crudFields\fields;


use kartik\detail\DetailView;
use yii\helpers\ArrayHelper;

class RadiobuttonGroup extends DropDown
{
    public function getField()
    {
        $field = parent::getField();
        if ($field === false ) {
            return false;
        }

        $field['items'] = array_filter($field['items']);

        return ArrayHelper::merge($field, [
            'type' => DetailView::INPUT_RADIO_BUTTON_GROUP,
        ]); // TODO: Change the autogenerated stub
    }
}