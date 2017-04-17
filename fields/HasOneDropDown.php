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
        /**
         * @var ActiveQuery $relationQuery
         */
        $relationQuery = $model->$relationName;
//        $relationQuery->via = null;
//        $relationQuery->link = null;
        $data = ArrayHelper::merge($data, ArrayHelper::map($relationQuery->all(),'id', $nameAttribute));

        return ArrayHelper::merge(parent::getField(), [
            'value' => $value,
            'data' => $data,
        ]);
    }


    public function getColumn() {
        return $this->getField();
    }
}