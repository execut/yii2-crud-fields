<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 2/27/19
 * Time: 1:45 PM
 */

namespace execut\crudFields\widgets;


use execut\yii\web\AssetBundle;
use yii\jui\JuiAsset;

class DropDownLinkAsset extends AssetBundle
{
    public $depends = [
        JuiAsset::class,
    ];
    public function init()
    {
        $this->basePath = __DIR__;
    }
}