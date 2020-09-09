<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields\HasManyMultipleInput;

use execut\crudFields\fields\HasManyMultipleInput\GridRenderer\Params;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;

class GridRenderer
{
    protected $defaultOptions;
    protected Params $params;
    public function __construct($defaultOptions)
    {
        $this->defaultOptions = $defaultOptions;
    }

    public function setParams(Params $options)
    {
        $this->params = $options;
    }

    /**
     * Returns calculated GridView options
     * @return array
     */
    protected function getDefaultOptions(): array
    {
        $gridOptions = $this->defaultOptions;
        if (is_callable($gridOptions)) {
            $gridOptions = $gridOptions();
        }

        return $gridOptions;
    }

    public function render(): ?string
    {
        $gridOptions = $this->params->toArray();
        if ($gridOptions === null) {
            return null;
        }
        $gridOptions = ArrayHelper::merge($gridOptions, $this->getDefaultOptions());
        $widgetClass = GridView::class;
        if (!empty($gridOptions['class'])) {
            $widgetClass = $gridOptions['class'];
        }

        return $widgetClass::widget($gridOptions);
    }
}