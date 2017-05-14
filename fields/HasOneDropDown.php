<?php
/**
 */

namespace execut\crudFields\fields;


use kartik\detail\DetailView;
use kartik\grid\GridView;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii\web\JsExpression;

class HasOneDropDown extends HasOneRelation
{
    public $url = null;
    public function getField() {
        $model = $this->model;
        $attribute = $this->attribute;
        $relationName = $this->relation;
        $nameAttribute = $this->nameAttribute;
        $value = null;
        if (!empty($model->$attribute)) {
            $value = $model->$attribute;
        }

        $data = ['' => ''];
        $relationGetter = 'get' . ucfirst($relationName);
        /**
         * @var ActiveQuery $relationQuery
         */
        $relationQuery = $model->$relationGetter();
        $class = $relationQuery->modelClass;
        $relationQuery = $class::find();

        $data = ArrayHelper::merge($data, ArrayHelper::map($relationQuery->all(),'id', $nameAttribute));
        $config = [
            'attribute' => $this->attribute,
            'value' => $value,
            'data' => $data,
        ];

        return ArrayHelper::merge([], $config);
    }


    public function getColumn() {
        $field = $this->getField();
        $data = $field['data'];
        unset($field['data']);
        $field['filter'] = $data;

        return $field;
    }
}