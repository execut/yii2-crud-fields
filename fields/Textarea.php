<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 12/26/17
 * Time: 10:46 AM
 */

namespace execut\crudFields\fields;


use kartik\detail\DetailView;
use yii\helpers\ArrayHelper;

class Textarea extends StringField
{
    public $maxLength = false;
    public function getField()
    {
        $parentField = parent::getField();
        if ($parentField === false) {
            return false;
        }

        $field = [
            'type' => DetailView::INPUT_TEXTAREA,
        ];
        return ArrayHelper::merge($parentField, $field);
    }
}