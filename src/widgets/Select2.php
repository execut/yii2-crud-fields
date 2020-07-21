<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\widgets;

use yii\helpers\Url;

/**
 * Select2 with DropDownLink support
 * @package execut\crudFields\widgets
 */
class Select2 extends \kartik\select2\Select2
{
    /**
     * @var string Id attribute name
     */
    public $idAttribute = 'id';
    /**
     * @var string Target url
     */
    public $url = null;
    /**
     * @var string Is render links
     */
    public $isRenderLink = true;

    /**
     * {@inheritDoc}
     */
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

    /**
     * Init dropDownLink widget addon
     * @throws \Exception
     */
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
