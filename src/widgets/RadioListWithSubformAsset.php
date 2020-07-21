<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\widgets;

use execut\yii\web\AssetBundle;

/**
 * Class RadioListWithSubformAsset
 * @package execut\crudFields\widgets
 */
class RadioListWithSubformAsset extends AssetBundle
{
    /**
     * {@inheritDoc}
     */
    public function init()
    {
        $this->basePath = __DIR__;

        parent::init();
    }
}
