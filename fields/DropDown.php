<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 6/27/17
 * Time: 5:31 PM
 */

namespace execut\crudFields\fields;


use kartik\detail\DetailView;
use unclead\multipleinput\MultipleInputColumn;
use yii\helpers\ArrayHelper;

class DropDown extends Field
{
    public $multipleInputType = MultipleInputColumn::TYPE_DROPDOWN;
    public function getField() {
        $field = parent::getField();
        if ($field === false) {
            return false;
        }

        $model = $this->model;
        $attribute = $this->attribute;
        $value = null;
        if (!empty($model->$attribute)) {
            $value = $model->$attribute;
        }

//        $columnValue = $this->getRelationObject()->getColumnValue();
        $data = $this->getDataWithEmptyStub();
        $config = [
            'type'=> DetailView::INPUT_DROPDOWN_LIST,
            'attribute' => $attribute,
//            'options' => [
//                'prompt' => '',
//            ],
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

        return ArrayHelper::merge(parent::getField(), $config);
    }

    public function getColumn() {
        if ($this->_column === false) {
            return false;
        }

        $data = $this->getData();
        $config = [
            'attribute' => $this->attribute,
            'filter' => $data,
        ];
        if ($this->valueAttribute !== null) {
            $config['value'] = $this->valueAttribute;
        } else {
            $config['value'] = function ($row) use ($data) {
                $attribute = $this->attribute;
                if (!empty($data[$row->$attribute])) {
                    return $data[$row->$attribute];
                }
            };
        }

        return ArrayHelper::merge(parent::getColumn(), $config);
    }

    public function getMultipleInputField() {
        $multipleInputField = parent::getMultipleInputField();
        if ($multipleInputField === false) {
            return $multipleInputField;
        }

        $data = $this->getDataWithEmptyStub();

        $config = [
            'name' => $this->attribute,
            'items' => $data,
        ];

        return ArrayHelper::merge($multipleInputField, $config);
    }

    protected $emptyDataStub = null;
    public function setEmptyDataStub($stub) {
        $this->emptyDataStub = $stub;

        return $this;
    }

    /**
     * @return array
     */
    protected function getEmptyDataStub()
    {
        if ($this->emptyDataStub !== null) {
            return $this->emptyDataStub;
        }

        return ['' => $this->getLabel()];
    }

    /**
     * @return array
     */
    protected function getDataWithEmptyStub(): array
    {
        $data = $this->getData();
        $data = ArrayHelper::merge($this->getEmptyDataStub(), $data);
        return $data;
    }
}