<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 6/27/17
 * Time: 5:31 PM
 */

namespace execut\crudFields\fields;


use kartik\detail\DetailView;
use yii\helpers\ArrayHelper;

class DropDown extends Field
{
    public function getField() {
        $model = $this->model;
        $attribute = $this->attribute;
        $value = null;
        if (!empty($model->$attribute)) {
            $value = $model->$attribute;
        }

        $columnValue = $this->getRelationObject()->getColumnValue();
        $config = [
            'type'=> DetailView::INPUT_DROPDOWN_LIST,
            'attribute' => $this->attribute,
//            'value' => $value,
//            'value' => $model, $columnValue,
            'items' => $this->getData(),
        ];

        return ArrayHelper::merge([], $config);
    }


    public function getColumn() {
        $field = $this->getField();
        $data = $field['items'];
        unset($field['items']);
        unset($field['type']);
        $field['value'] = $this->getRelationObject()->valueAttribute;
        $field['filter'] = $data;

        return $field;
    }
}