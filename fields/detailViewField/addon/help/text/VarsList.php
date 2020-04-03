<?php
/**
 */

namespace execut\crudFields\fields\detailViewField\addon\help\text;


use execut\crudFields\fields\detailViewField\addon\help\Text;
use yii\helpers\Html;

class VarsList implements Text
{
    protected $info = null;
    protected $varsList = null;
    public function __construct($info, $varsList)
    {
        $this->info = $info;
        $this->varsList = $varsList;
    }

    public function getValue()
    {
        $varsListParts = [];
        foreach ($this->varsList as $key => $description) {
            $varsListParts[] = $key . ' - ' . $description;
        }

        $helpText = $this->info . Html::ul($varsListParts);

        return $helpText;
    }

    public function getInfo() {
        return $this->info;
    }

    public function getVarsList() {
        return $this->varsList;
    }
}