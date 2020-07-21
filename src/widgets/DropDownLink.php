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

/**
 * Class for render links to records inside Select2 widget
 *
 * @package execut\crudFields
 */
class DropDownLink extends Widget
{
    /**
     * @var array Target records url
     */
    public $url = null;
    /**
     * @var string Target records id attribute
     */
    public $idAttribute = 'id';

    /**
     * Run
     * @return string
     */
    public function run()
    {
        $url = $this->url;
        $url[$this->idAttribute] = '{id}';
        $this->clientOptions['url'] = Url::to($url);
        $this->registerWidget();
        return Html::a('>>>', '', $this->options);
    }
}
