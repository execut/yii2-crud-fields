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

//        $columnValue = $this->getRelationObject()->getColumnValue();
        $data = $this->getData();
        $config = [
            'type'=> DetailView::INPUT_DROPDOWN_LIST,
            'attribute' => $attribute,
//            'model' => $this->model,
//            'viewModel' => $this->model,
            'value' => function () use ($data, $value) {
                if (!empty($data[$value])) {
                    return $data[$value];
                }
            },
//            'value' => $model, $columnValue,
            'items' => $data,
        ];

        return ArrayHelper::merge([], $config);
    }


    public function getColumn() {
        $model = $this->model;
        $attribute = $this->attribute;

        $config = [
            'attribute' => $this->attribute,
            'value' => $this->valueAttribute,
            'filter' => $this->getData(),
        ];

        return $config;
    }
}