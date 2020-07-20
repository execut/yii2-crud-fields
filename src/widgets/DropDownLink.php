<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\widgets;
use execut\yii\jui\Widget;
use yii\helpers\Html;
use yii\helpers\Url;
class DropDownLink extends Widget
{
    public $url = null;
    public $idAttribute = 'id';
    public function run()
    {
        $url = $this->url;
        $url[$this->idAttribute] = '{id}';
        $this->clientOptions['url'] = Url::to($url);
        $this->registerWidget();
        return Html::a('>>>', '', $this->options);
    }
}