<?php
/**
 */

namespace execut\crudFields\fields;


use execut\crudFields\Relation;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\mysql\Schema;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

class StringField extends Field
{
    public $maxLength = 255;
//    public $isPartially = true;
    public function applyScopes(ActiveQuery $query) {
        $value = $this->getValue();
        if ($value) {
//            if ($this->isPartially) {
//                $value = '%' . $value . '%';
//            }

            $attribute = $this->attribute;
            if (($db = $this->model->getDb()) && ($schema = $db->getSchema()) && $schema instanceof Schema) {
                $operator = 'LIKE';
            } else {
                $operator = 'ILIKE';
            }

            $query->andWhere([
                $operator,
                $attribute,
                $value,
            ]);
        }

        return $query;
    }

    protected function getRules():array
    {
        $rules = parent::getRules();
        if ($this->maxLength) {
            $rules['maxLength_' . $this->attribute] = [[$this->attribute], 'string', 'max' => $this->maxLength];
        }

        return $rules;
    }
}