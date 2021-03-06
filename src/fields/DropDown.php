<?php
/**
 * @author Mamaev Yuriy (eXeCUT)
 * @link https://github.com/execut
 * @copyright Copyright (c) 2020 Mamaev Yuriy (eXeCUT)
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
namespace execut\crudFields\fields;

use kartik\detail\DetailView;
use unclead\multipleinput\MultipleInputColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Select dropdown field
 * @package execut\crudFields
 */
class DropDown extends Field
{
    /**
     * {@inheritdoc}
     */
    public $multipleInputType = MultipleInputColumn::TYPE_DROPDOWN;
    /**
     * @var array|null Item for empty value
     */
    protected $emptyDataStub = null;

    /**
     * {@inheritdoc}
     */
    public function getField()
    {
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

        $data = $this->getDataWithEmptyStub();
        $config = [
            'type'=> DetailView::INPUT_DROPDOWN_LIST,
            'attribute' => $attribute,
            'value' => function () use ($data, $value) {
                if (!empty($data[$value])) {
                    return $data[$value];
                }

                return null;
            },
            'items' => $data,
        ];
        $field = ArrayHelper::merge($config, $field);

        return $field;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumn()
    {
        if ($this->_column === false) {
            return false;
        }

        $data = $this->getDataWithEmptyStub();
        $config = [
            'attribute' => $this->attribute,
            'filter' => $this->renderHasRelationFilter() . Html::activeDropDownList($this->model, $this->attribute, $data),
        ];
        if ($this->valueAttribute !== null) {
            $config['value'] = $this->valueAttribute;
        } else {
            $config['value'] = function ($row) use ($data) {
                $attribute = $this->attribute;
                if (!empty($data[$row->$attribute])) {
                    return $data[$row->$attribute];
                }

                return null;
            };
        }

        $config = ArrayHelper::merge(parent::getColumn(), $config);

        return $config;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = parent::rules();
        if ($this->isHasRelationAttribute) {
            $rules[$this->isHasRelationAttribute . 'safe'] = [
                [$this->isHasRelationAttribute],
                'safe',
                'on' => self::SCENARIO_GRID,
            ];
        }

        return $rules;
    }

    /**
     * {@inheritdoc}
     */
    public function getMultipleInputField()
    {
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

    /**
     * Set item for empty value
     * @param array $stub
     * @return $this
     */
    public function setEmptyDataStub($stub)
    {
        $this->emptyDataStub = $stub;

        return $this;
    }

    /**
     * Get item for empty value
     * @return array
     */
    protected function getEmptyDataStub()
    {
        if ($this->emptyDataStub !== null) {
            return $this->emptyDataStub;
        }

        return ['' => ''];
    }

    /**
     * Get items of list with empty data stub
     * @return array
     */
    protected function getDataWithEmptyStub(): array
    {
        $data = $this->getData();
        $data = ArrayHelper::merge($this->getEmptyDataStub(), $data);
        return $data;
    }
}
