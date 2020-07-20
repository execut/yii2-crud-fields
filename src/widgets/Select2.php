<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 2/27/19
 * Time: 1:40 PM
 */

namespace execut\crudFields\widgets;


use execut\yii\jui\WidgetTrait;
use yii\helpers\Html;
use yii\helpers\Url;

class Select2 extends \kartik\select2\Select2
{
    public $idAttribute = 'id';
    public $url = null;
    public $isRenderLink = true;

    public function run()
    {
        $this->initDropDownLinkAddon();
        if ($this->url) {
            $this->pluginOptions['ajax']['url'] = Url::to($this->url);
        }

        $id = $this->id . '-execut';
        Select2ExecutAsset::register($this->view);
        echo '<div class="select2-execut" id="' . $id . '">';
        if ($this->value === []) {
            $this->value = null;
        }

        parent::run();
        echo '<div class="select-label"></div></div>';
        $this->view->registerJs(<<<JS
$("#$id").Select2Execut();
JS
        );
    }

    protected function initDropDownLinkAddon(): void
    {
        if (empty($this->url) || !$this->isRenderLink) {
            return;
        }

        $urlParams = $this->url;
        $urlParams[0] = $urlParams[0] . '/update';
        if (empty($this->addon)) {
            $this->addon = [
                'append' => [
                    'content' => DropDownLink::widget([
                        'id' => $this->id . '_link',
                        'idAttribute' => $this->idAttribute,
                        'url' => $urlParams
                    ]),
                ],
            ];
        }
    }
}