<?php
/**
 */

namespace execut\crudFields\fields;


use execut\crudFields\Relation;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

class Password extends Field
{
    public function getField()
    {
        $field = parent::getField();
        if ($field === false) {
            return false;
        }

        return ArrayHelper::merge($field, [
            'options' => $this->getOptions(),
        ]);
    }

    public function getMultipleInputField()
    {
        $result = parent::getMultipleInputField(); // TODO: Change the autogenerated stub
        $result['options'] = $this->getOptions();

        return $result;
    }

    /**
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            'type' => 'password',
        ];
    }

    public function getColumn()
    {
        return false;
    }
}