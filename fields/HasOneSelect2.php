<?php
/**
 */

namespace execut\crudFields\fields;


use kartik\detail\DetailView;
use kartik\grid\GridView;
use kartik\select2\Select2;
use unclead\multipleinput\MultipleInputColumn;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii\web\JsExpression;

class HasOneSelect2 extends Field//implements Container
{
    public $url = null;
    public $nameParam = null;
    public $isNoRenderRelationLink = false;
    public $createUrl = null;
    public function getField() {
        $field = parent::getField();
        if ($field === false) {
            return $field;
        }

        $widgetOptions = $this->getSelect2WidgetOptions();
        $rowOptions = [];
        if ($this->getDisplayOnly() && empty($this->getValue())) {
            $type = DetailView::INPUT_HIDDEN;
            $rowOptions['style'] = 'display:none';
        } else {
            $type = DetailView::INPUT_SELECT2;
            if ($this->createUrl) {
                $widgetOptions['addon'] = [
                    'append' => [
                        'content' => $this->getCreateButton(),
                        'asButton' => true
                    ]
                ];
            }
//            $widgetOptions['data'][''] = '';
        }

//        if ($this->isRenderRelationFields) {
//            $relationName = $this->getRelationObject()->getName();
//            $widgetOptions['pluginEvents'] = [
//                'change' => new JsExpression(<<<JS
//        function () {
//            var el = $(this),
//                inputs = $('.related-$relationName input').not(el),
//                parents = inputs.not(el).attr('disabled', 'disabled').parent().parent().parent().parent().parent();
//            if (el.val()) {
//                inputs.attr('disabled', 'disabled');
//                parents.hide();
//            } else {
//                inputs.attr('disabled', false).val('');
//                parents.show();
//            }
//        }
//JS
//                )
//            ];
//        }

//        $sourceInitText = $this->getRelationObject()->getSourceText();
        $field = ArrayHelper::merge([
            'type' => $type,
            'value' => $this->getRelationObject()->getColumnValue($this->model),
            'format' => 'raw',
            'widgetOptions' => $widgetOptions,
            'fieldConfig' => [
//                'template' => "{input}$createButton\n{error}\n{hint}",
            ],
            'displayOnly' => $this->getIsRenderRelationFields(),
            'rowOptions' => $rowOptions,
        ], $field);

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
        $column = parent::getColumn();
        if ($column === false) {
            return false;
        }

        $sourceInitText = $this->getRelationObject()->getSourcesText();

//        $sourcesNameAttribute = $modelClass::getFormAttributeName('name');
        if (empty($this->attribute)) {
            throw new Exception('Attribute is required');
        }

        return ArrayHelper::merge([
            'attribute' => $this->attribute,
            'format' => 'raw',
//            'value' => $this->getData(),
            'value' => function ($row) {
                return $this->getRelationObject()->getColumnValue($row);
            },
//                'value' => function () {
//                    return 'asdasd';
//                },
            'filter' => $sourceInitText,
            'filterType' => GridView::FILTER_SELECT2,
            'filterWidgetOptions' => $this->getSelect2WidgetOptions(),
        ], $column);
    }

    public function getLanguage() {
        return substr(\yii::$app->language, 0, 2);
    }

    public function getMultipleInputField()
    {
        return [
            'type' => Select2::class,
            'name' => $this->attribute,
            'options' => $this->getSelect2WidgetOptions(),
        ];
    }

    /**
     * @return array
     */
    protected function getSelect2WidgetOptions(): array
    {
        $sourceInitText = $this->getRelationObject()->getSourcesText();
        $nameParam = $this->getNameParam();
        $widgetOptions = [
            'theme' => Select2::THEME_BOOTSTRAP,
            'language' => $this->getLanguage(),
            'initValueText' => $sourceInitText,
            'pluginOptions' => [
                'allowClear' => true,
            ],
            'options' => [
                'placeholder' => $this->getLabel(),
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
            $data = $this->getRelationObject()->getData();
            $widgetOptions = ArrayHelper::merge($widgetOptions, [
                'data' => $data,
            ]);
        }

        return $widgetOptions;
    }

    /**
     * @return string
     */
    protected function getCreateButton(): string
    {
        return Html::a('Создать', $this->createUrl, [
            'class' => 'btn btn-primary',
            'title' => 'Создать новый автомобиль',
            'data-toggle' => 'tooltip',
            'target' => '_blank',
        ]);
    }
}