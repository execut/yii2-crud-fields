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
    protected $_relation = null;
    public function setRelation($relation) {
        $this->_relation = $relation;

        return $this;
    }

    public function getRelation() {
        if ($this->_relation === null) {
            $this->_relation = $this->getRelationNameFromAttribute();
        }

        return $this->_relation;
    }

    protected function getRelationNameFromAttribute() {
        $attribute = $this->attribute;
        $relationName = lcfirst(Inflector::id2camel(str_replace('_id', '', $attribute), '_'));

        return $relationName;
    }
}