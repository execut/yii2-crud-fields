<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 10/1/19
 * Time: 4:15 PM
 */

namespace execut\crudFields\widgets;

use execut\yii\web\AssetBundle;
use yii\jui\JuiAsset;

class HasRelationDropdownAsset extends AssetBundle
{
    public $depends = [
        JuiAsset::class,
    ];
}