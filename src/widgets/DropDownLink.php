<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 2/27/19
 * Time: 3:00 PM
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