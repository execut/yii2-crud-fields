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

class HasOneRelation extends Field
{
    public $nameAttribute = 'name';
    public $url = null;
    public function getField() {
        $sourceInitText = '';
        $model = $this->model;
        $attribute = $this->attribute;
        $relationName = $this->getRelationNameFromAttribute();
        $nameAttribute = $this->nameAttribute;
        if (!empty($model->$attribute)) {
            $sourceInitText = $model->$relationName->$nameAttribute;
        }

        return ArrayHelper::merge(parent::getField(), [
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
        "name": params.term
    };
}
JS
                        )
                    ],
                ],
            ],
        ]);
    }


    public function getColumn() {
        $relationName = $this->getRelationNameFromAttribute();
        $model = $this->model;
        $modelClass = $model->getRelation($relationName)->modelClass;

        $sourceInitText = [];
        $attribute = $this->attribute;
        $nameAttribute = $this->nameAttribute;
        if (!empty($model->$attribute)) {
            $sourceIds = [];
            if (is_array($model->$attribute)) {
                $sourceIds = $model->$attribute;
            } else {
                $sourceIds[] = $model->$attribute;
            }

            $models = $modelClass::find()->andWhere(['id' => $sourceIds])->all();

            $sourceInitText = ArrayHelper::map($models, 'id', $nameAttribute);
        }

//        $sourcesNameAttribute = $modelClass::getFormAttributeName('name');

        return [
            'attribute' => $attribute,
            'value' => $relationName . '.' . $nameAttribute,
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
    "name": params.term
  };
}
JS
                        )

                    ],
                ],
            ],
        ];
    }

    protected function getRelationNameFromAttribute() {
        $attribute = $this->attribute;
        $relationName = lcfirst(Inflector::id2camel(str_replace('_id', '', $attribute), '_'));

        return $relationName;
    }
}