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
    public $minLength = null;
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
        if ($this->maxLength || $this->minLength) {
            $rule = [[$this->attribute], 'string'];
            if ($this->maxLength) {
                $rule['max'] = $this->maxLength;
            }

            if ($this->minLength) {
                $rule['min'] = $this->minLength;
            }

            $rules['string_' . $this->attribute] = $rule;
        }

        return $rules;
    }
}