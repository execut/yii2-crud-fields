<?php
/**
 */

namespace execut\crudFields\fields;


use kartik\detail\DetailView;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii\web\JsExpression;

class HasOneSelect2 extends Field
{
    public $url = null;
    public $nameParam = null;
    public function getField() {
        if (!empty($this->_field)) {
            return $this->_field;
        }

        $sourceInitText = $this->getRelationObject()->getSourceText();
        $nameParam = $this->getNameParam();
        return ArrayHelper::merge([
            'type' => DetailView::INPUT_SELECT2,
            'value' => $sourceInitText,
            'widgetOptions' => [
                'initValueText' => $sourceInitText,
                'pluginOptions' => [
                    'allowClear' => true,
                    'placeholder' => '',
                    'ajax' => [
                        'url' => Url::to($this->url),
                        'dataType' => 'json',
                        'data' => new JsExpression(<<<JS
function(params) {
    return {
        "$nameParam": params.term
    };
}
JS
                        )
                    ],
                ],
            ],
        ], parent::getField());
    }

    public function getNameParam() {
        if ($this->nameParam !== null) {
            return $this->nameParam;
        }

        $formName = $this->getRelationObject()->getRelationFormName();

        return $formName . '[' . $this->nameAttribute . ']';
    }


    public function getColumn() {
        $sourceInitText = $this->getRelationObject()->getSourcesText();

//        $sourcesNameAttribute = $modelClass::getFormAttributeName('name');
        $nameParam = $this->getNameParam();

        return ArrayHelper::merge([
            'attribute' => $this->attribute,
            'value' => $this->getRelationObject()->getColumnValue(),
//                'value' => function () {
//                    return 'asdasd';
//                },
            'filter' => $sourceInitText,
            'filterType' => GridView::FILTER_SELECT2,
            'filterWidgetOptions' => [
                'initValueText' => $sourceInitText,
                'options' => [
                    'multiple' => true,
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'ajax' => [
                        'url' => Url::to($this->url),
                        'dataType' => 'json',
                        'data' => new JsExpression(<<<JS
function (params) {
  return {
    "$nameParam": params.term
  };
}
JS
                        )

                    ],
                ],
            ],
        ], parent::getColumn());
    }
}