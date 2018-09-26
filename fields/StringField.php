<?php
/**
 */

namespace execut\crudFields\fields;


use execut\crudFields\Relation;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

class StringField extends Field
{
//    public $isPartially = true;
    public function applyScopes(ActiveQuery $query) {
        $value = $this->getValue();
        if ($value) {
//            if ($this->isPartially) {
//                $value = '%' . $value . '%';
//            }

            $attribute = $this->attribute;
            $query->andWhere([
                'ILIKE',
                $attribute,
                $value,
            ]);
        }

        return $query;
    }

    protected function getRules():array
    {
        $rules = parent::getRules();
        $rules['maxLength_' . $this->attribute] = [[$this->attribute], 'string', 'max' => 255];

        return $rules;
    }
}