<?php
/**
 */

namespace execut\crudFields\fields;


use detalika\cars\models\ModificationsVsEngine;
use kartik\detail\DetailView;
use kartik\select2\Select2;
use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\MultipleInputColumn;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii\web\JsExpression;

class HasManySelect2 extends HasOneSelect2
{
    public $columns = null;

    public $viaColumns = [];

    public function getColumn()
    {
        return parent::getColumn(); // TODO: Change the autogenerated stub
    }

    public function applyScopes(ActiveQuery $query)
    {
        $this->applyRelationScopes($query);

        return $query;
    }

    public function getField()
    {
        $sourceInitText = $this->getRelationObject()->getSourcesText();
        $relation = $this->getRelationObject();
        if ($relation->isVia()) {
            $viaRelationModelClass = $this->getRelationObject()->getViaRelationQuery()->modelClass;
            $viaRelationModel = new $viaRelationModelClass;
            $attribute = $this->getRelationObject()->getViaRelation();

            $fromAttribute = $this->getRelationObject()->getViaFromAttribute();
            $toAttribute = $this->getRelationObject()->getViaToAttribute();
            $relationName = $this->getRelationObject()->getName();
            if (empty($this->model->$attribute)) {
                $viaModelsAttributes = [];
                foreach ($this->model->$relationName as $model) {
                    $viaModelsAttributes[] = [
                        $fromAttribute => $model->primaryKey
                    ];
                }

                $this->model->$attribute = $viaModelsAttributes;
            }
        } else {
            $relationQuery = $this->getRelationObject()->getRelationQuery();
            $viaRelationModelClass = $relationQuery->modelClass;
            $viaRelationModel = new $viaRelationModelClass;
            $attribute = $this->getRelationObject()->getName();

            $fromAttribute = key($relationQuery->link);
            $toAttribute = current($relationQuery->link);
        }

        $columns = ArrayHelper::merge([
            'from' => [
                'name' => $fromAttribute,
                'type' => MultipleInputColumn::TYPE_HIDDEN_INPUT,
                'defaultValue' => $this->model->id,
            ],
            'to' => [
                'name' => $toAttribute,
                'type' => Select2::class,
                'defaultValue' => null,
                'value' => $sourceInitText,
                'options' => $this->getSelect2WidgetOptions(),
            ],
        ], $this->viaColumns);

        if ($toAttribute !== 'id' && empty($columns['id'])) {
            $columns['id'] = [
                'name' => 'id',
                'type' => MultipleInputColumn::TYPE_HIDDEN_INPUT,
            ];
        }

        foreach ($columns as &$column) {
            if (!isset($column['title']) && !empty($column['name'])) {
                $column['title'] = Html::activeLabel($viaRelationModel, $column['name']);
            }
        }

        return [
            'type' => DetailView::INPUT_WIDGET,
            'attribute' => $attribute,
            'label' => $this->getLabel(),
            'format' => 'raw',
            'value' => function () {
//                $dataProvider = new ActiveDataProvider();
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
                'addButtonPosition' => MultipleInput::POS_HEADER,
                'columns' => $columns
            ],
        ];
    }

    public function getMultipleInputField()
    {
        return false;
    }
}