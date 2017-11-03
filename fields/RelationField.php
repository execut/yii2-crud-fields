<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 10/19/17
 * Time: 1:35 PM
 */

namespace execut\crudFields\fields;


use execut\crudFields\columns\RelationColumn;
use execut\yii\helpers\ArrayHelper;
use yii\db\ActiveQuery;
use yii\helpers\Html;

class RelationField extends Field
{
    public function getColumn()
    {
        $modelClass = $this->getRelationObject()->getRelationModelClass();
        $model = new $modelClass;

        $fields = $model->getBehavior('fields')->getFields();
        $columns = [];
        foreach ($fields as $field) {
            $columns[] = $field->getMultiple();
        }

        return ArrayHelper::merge(parent::getField(), [
            'class' => RelationColumn::class,
            'attributePrefix' => 'contacts[0]',
            'value' => $this->getRelationObject()->getColumnValue(),
            'relatedColumns' => $columns,
        ]);
    }

    public function getField()
    {
        return false;
    }

    public function applyScopes(ActiveQuery $query)
    {
        return $query->with($this->relation);
    }
}