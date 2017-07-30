<?php
/**
 */

namespace execut\crudFields\fields;


use execut\crudFields\Relation;
use yii\base\Object;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\helpers\Inflector;

class StringField extends Field
{
    public function applyScopes(ActiveQuery $query) {
        $value = $this->getValue();
        if ($value) {
            $attribute = $this->attribute;
            $query->andWhere([
                'ILIKE',
                $attribute,
                $this->model->$attribute,
            ]);
        }

        return $query;
    }
}