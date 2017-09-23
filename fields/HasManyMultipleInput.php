<?php
/**
 */

namespace execut\crudFields\fields;


use kartik\detail\DetailView;
use kartik\grid\BooleanColumn;
use kartik\grid\GridView;
use kartik\select2\Select2;
use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\MultipleInputColumn;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

class HasManyMultipleInput extends Field
{
    public $url = null;
    public $columns = [
        'id' => [
            'attribute' => 'id',
        ],
        'name' => [
            'attribute' => 'name',
        ],
        'visible' => [
            'class' => BooleanColumn::class,
            'attribute' => 'visible'
        ],
    ];

    public $toAttribute = null;

    public $viaColumns = [
    ];
    public function getField()
    {
        $nameParam = $this->getNameParam();
        $relation = $this->getRelationObject();
        $attribute = $this->relation;
        if ($relation->isVia()) {
            $fromAttribute = $relation->getViaFromAttribute();
            $toAttribute = $relation->getViaToAttribute();
            $sourceInitText = $relation->getSourcesText();
            $viaRelationModelClass = $relation->getRelationModelClass();
            $viaRelationModel = new $viaRelationModelClass;
            $changeEvent = new JsExpression(<<<JS
    function () {
        var el = $(this),
            inputs = el.parent().parent().parent().find('input, select');
        if (el.val()) {
            inputs.not(el).attr('disabled', 'disabled');
        } else {
            inputs.not(el).attr('disabled', false);
        }
    }
JS
            );
            $targetFields = [
                //            [
                //                'name' => $toAttribute,
                //                'type' => MultipleInputColumn::TYPE_HIDDEN_INPUT,
                //                'defaultValue' => $this->model->id,
                //            ],
                'id' => [
                    'name' => 'id',
                    'type' => Select2::class,
                    'defaultValue' => null,
                    'value' => $sourceInitText,
                    'headerOptions' => [
                        'style' => 'width: 150px;',
                    ],
                    'options' => [
                        'initValueText' => $sourceInitText,
                        'pluginEvents' => [
                            'change' => $changeEvent,
                        ],
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
                ],
            ];
            $columns = ArrayHelper::merge($targetFields, $viaRelationModel->getMultipleInputFields(), $this->viaColumns);

            foreach ($columns as &$column) {
                if (empty($column['title']) && !empty($column['name'])) {
                    $column['title'] = Html::activeLabel($viaRelationModel, $column['name']);
                }
            }
        } else {
            $viaRelationModelClass = $relation->getRelationModelClass();
            $viaRelationModel = new $viaRelationModelClass;
            $columns = ArrayHelper::merge([
                [
                    'type' => MultipleInputColumn::TYPE_HIDDEN_INPUT,
                    'name' => 'id',
                ],
            ], $viaRelationModel->getMultipleInputFields(), $this->viaColumns);
        }

        $field = parent::getField();
        if (!is_array($field)) {
            return $field;
        }

        return ArrayHelper::merge([
            'type' => DetailView::INPUT_WIDGET,
            'attribute' => $attribute,
//            'label' => $this->getLabel(),
            'format' => 'raw',
            'value' => function () {
                $dataProvider = new ActiveDataProvider();
//                $query = $this->model->getRelation($this->relation);
//                $dataProvider->query = $query;
//                return GridView::widget([
//                    'dataProvider' => $dataProvider,
//                    'columns' => $this->columns,
//                ]);
            },
            'widgetOptions' => [
                'class' => MultipleInput::className(),
                'allowEmptyList' => true,
                'enableGuessTitle' => true,
                'model' => $viaRelationModel,
                'addButtonPosition' => MultipleInput::POS_ROW,
                'columns' => $columns
            ],
        ], $field);
    }

    public $nameParam = null;

    /**
     * @TODO Copy past from HasOneSelect2
     *
     * @return null|string
     */
    public function getNameParam() {
        if ($this->nameParam !== null) {
            return $this->nameParam;
        }

        $formName = $this->getRelationObject()->getRelationFormName();

        return $formName . '[' . $this->nameAttribute . ']';
    }

    public function applyScopes(ActiveQuery $query)
    {
        return $this->applyRelationScopes($query);
    }


    public function getColumn() {
        $sourceInitText = $this->getRelationObject()->getSourcesText();

//        $sourcesNameAttribute = $modelClass::getFormAttributeName('name');
        $nameParam = $this->getNameParam();

        return [
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
                $result = [];
                foreach ($row->$attribute as $vsKeyword) {
                    $currentUrl = [$url . '/update', 'id' => $vsKeyword->id];

                    $value = ArrayHelper::getValue($vsKeyword, $this->nameAttribute);

                    $result[] = Html::a($value, Url::to($currentUrl));
                }

                return implode(', ', $result);
            },
//            'value' => function ($row) {
//                $url = $this->url;
//                if (is_array($url)) {
//                    $url = $url[0];
//                } else {
//                    $url = str_replace('/index', '', $url);
//                }
//
//                $attribute = $this->attribute;
//
//                $url = [$url . '/update', 'id' => $row->$attribute];
//
//                $valueAttribute = $this->getRelationObject()->getColumnValue();
//                $value = ArrayHelper::getValue($row, $valueAttribute);
//
//                return Html::a($value, Url::to($url));
//            },
//                'value' => function () {
//                    return 'asdasd';
//                },
            'filter' => $sourceInitText,
            'filterType' => GridView::FILTER_SELECT2,
            'filterWidgetOptions' => [
//                'language' => $this->getLanguage(),
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
        ];
    }
}