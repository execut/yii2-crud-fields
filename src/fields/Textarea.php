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
    protected $_field = [
        'type' => DetailView::INPUT_TEXTAREA,
    ];
}