<?php
/**
 */

namespace execut\crudFields\fields\detailViewField\addon\help\text;


use execut\crudFields\fields\detailViewField\addon\help\Text;

class FromSetting implements Text
{
    protected $key = null;
    public function __construct($key)
    {
        $this->key = $key;
    }

    public function getValue()
    {
        return \yii::$app->settings->get($this->key);
    }
}