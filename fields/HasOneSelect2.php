<?php
/**
 */

namespace execut\crudFields\fields;


use kartik\detail\DetailView;
use kartik\grid\GridView;
use kartik\select2\Select2;
use unclead\multipleinput\MultipleInputColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii\web\JsExpression;

class HasOneSelect2 extends Field//implements Container
{
    public $url = null;
    public $nameParam = null;
    public function getField() {
        $widgetOptions = $this->getSelect2WidgetOptions();
        $sourceInitText = $this->getRelationObject()->getSourceText();

        $field = [
            'type' => DetailView::INPUT_SELECT2,
            'value' => $sourceInitText,
            'widgetOptions' => $widgetOptions,
        ];


        $field = ArrayHelper::merge($field, parent::getField());

        return $field;
    }

//    public function getFields() {
//        $relationModelClass = $this->getRelationObject()->getRelationModelClass();
//        $relationModel = new $relationModelClass;
//
//        return $relationModel->getBehavior('fields')->getFields();
//    }

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
            'format' => 'html',
            'value' => function ($row) {
                $url = $this->url;
                if (is_array($url)) {
                    $url = $url[0];
                } else {
                    $url = str_replace('/index', '', $url);
                }

                $attribute = $this->attribute;

                $url = [$url . '/update', 'id' => $row->$attribute];

                $valueAttribute = $this->getRelationObject()->getColumnValue();
                $value = ArrayHelper::getValue($row, $valueAttribute);

                return Html::a($value, Url::to($url));
            },
//                'value' => function () {
//                    return 'asdasd';
//                },
            'filter' => $sourceInitText,
            'filterType' => GridView::FILTER_SELECT2,
            'filterWidgetOptions' => [
                'language' => $this->getLanguage(),
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
    "$nameParam": params.term,
    page: params.page
  };
}
JS
                        )

                    ],
                ],
            ],
        ], parent::getColumn());
    }

    public function getLanguage() {
        return substr(\yii::$app->language, 0, 2);
    }

    public function getMultipleInputField()
    {
        return [
            'type' => Select2::class,
            'name' => $this->getNameParam(),
            'options' => $this->getSelect2WidgetOptions(),
        ];
    }

    /**
     * @return array
     */
    protected function getSelect2WidgetOptions(): array
    {
        $sourceInitText = $this->getRelationObject()->getSourceText();
        $nameParam = $this->getNameParam();
        $widgetOptions = [
            'language' => $this->getLanguage(),
            'initValueText' => $sourceInitText,
            'pluginOptions' => [
                'allowClear' => true,
            ],
        ];

        if ($this->url !== null) {
            $widgetOptions = ArrayHelper::merge($widgetOptions, [
                'pluginOptions' => [
                    'ajax' => [
                        'url' => Url::to($this->url),
                        'dataType' => 'json',
                        'data' => new JsExpression(<<<JS
            function(params) {
                return {
                    "$nameParam": params.term,
                    page: params.page
                };
            }
JS
                        )
                    ]
                ],
            ]);
        } else {
            $widgetOptions = ArrayHelper::merge($widgetOptions, [
                'data' => $this->getRelationObject()->getData(),
            ]);
        }

        return $widgetOptions;
    }
}