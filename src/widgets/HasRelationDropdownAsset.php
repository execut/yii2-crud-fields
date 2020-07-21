<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\widgets;

use execut\yii\web\AssetBundle;
use yii\jui\JuiAsset;

/**
 * Class HasRelationDropdownAsset
 * @package execut\crudFields
 */
class HasRelationDropdownAsset extends AssetBundle
{
    /**
     * {@inheritDoc}
     */
    public $depends = [
        JuiAsset::class,
    ];
}
