<?php
/**
 */

namespace execut\crudFields\fields;


use kartik\depdrop\DepDrop;
use kartik\detail\DetailView;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii\web\JsExpression;

class HasOneDepDrop extends HasOneSelect2
{
    public $depends = [];
    public $dependedAttribute = null;
    public function getField()
    {
        $field = parent::getField();
        $widgetOptions = $field['widgetOptions'];
        unset($field['widgetOptions']);
        unset($widgetOptions['pluginOptions']['ajax']);
        return ArrayHelper::merge($field, [
            'type' => DetailView::INPUT_DEPDROP,
            'widgetOptions' => [
                'type' => DepDrop::TYPE_SELECT2,
                'data' => [$this->getValue() => $this->getRelationObject()->getSourceText()],
                'name' => $this->attribute,
                'value' => $this->value,
                'select2Options' => $widgetOptions,
                'pluginOptions' => [
//                    'initialize' => true,
                    'nameParam' => 'text',
                    'allParam' => $this->dependedAttribute,
                    'ajaxSettings' => [
                        'method' => 'get',
                    ],
                    'url' => Url::to($this->url),
                    'depends' => $this->depends,
                ],
            ],
        ]);
    }
}