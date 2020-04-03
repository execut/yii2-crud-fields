<?php
/**
 */

namespace execut\crudFields\fields\detailViewField\addon\help\text;


use execut\crudFields\fields\detailViewField\addon\help\Text;

class Simple implements Text
{
    protected $value = null;
    public function __construct($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }
}