<?php


namespace execut\crudFields\widgets;


use execut\yii\web\AssetBundle;
use yii\jui\JuiAsset;

class Select2ExecutAsset extends AssetBundle
{
    public $depends = [
        JuiAsset::class,
    ];
}